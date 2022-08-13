<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MechanicAd\BulkDestroyMechanicAd;
use App\Http\Requests\Admin\MechanicAd\DestroyMechanicAd;
use App\Http\Requests\Admin\MechanicAd\IndexMechanicAd;
use App\Http\Requests\Admin\MechanicAd\StoreMechanicAd;
use App\Http\Requests\Admin\MechanicAd\UpdateMechanicAd;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Ad,MechanicAd,DealerShowRoom,AdImage};

class MechanicAdsController extends Controller
{
    use ApiController;
    
    /**
     * Display a listing of the resource.
     *
     * @param IndexMechanicAd $request
     * @return array|Factory|View
     */
    public function index(IndexMechanicAd $request)
    {
        $promoted_simple_ads = MechanicAd::whereRaw('ad_id in(SELECT ad_id FROM promoted_simple_ads)')->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)')->inRandomOrder()->limit(25);

        foreach (MechanicAd::getRelationships() as $key => $value) {
           $promoted_simple_ads->with($key);
        }

        $promoted = $promoted_simple_ads->get()->toArray();
        
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MechanicAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address', 'geocoding_status'],

            // set columns to searchIn
            ['id', 'ad_id', 'address', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address', 'geocoding_status'],

            function ($query) use ($request) {
                        
                $columns =   ['id', 'ad_id', 'address', 'latitude', 'longitude', 'zip_code', 'city', 'country', 'mobile_number', 'whatsapp_number', 'website_url', 'email_address', 'geocoding_status'];
                
                foreach ($columns as $column) {
                    if ($request->filters) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
                }
                $query->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10)');

                if(isset($request->filters['title'])){
                    $ad_ids = Ad::select('id')
                        ->where('title','LIKE','%'.$request->filters['title'].'%' )
                        ->where('type','mechanic')
                        ->get()
                        ->toArray();
                    
                    $ids = [];

                    foreach ($ad_ids as $key => $ad_id) {
                        $ids[$key] =  $ad_id['id'];
                    }

                    $query->whereIn('ad_id',$ids);
                }

                foreach (MechanicAd::getRelationships() as $key => $value) {
                   $query->with($key);
                }

                $query->with(['ad' => function($query){
                    $query->with(['images']);
                }]);
            }
        );
        
        $data = $data->toArray(); 
            
        array_push($promoted,...$data['data']);
    
        $data['data'] = $promoted;

        return ['data' => $data];
    }

    public function searchLike(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'filter' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors()],422);
        }
        
        $filter = $request->filter;
       
        $data = MechanicAd::whereRaw("ad_id in (SELECT id FROM ads where (ads.title LIKE '%".$filter."%' or ads.description LIKE '%".$filter."%') and type = 'mechanic')")->with([
                    'ad'=> function($query)
                    {
                        $query->with(['images']);
                    }])->paginate(10);

        return response()->json([
            'data' => $data
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.mechanic-ad.create');

        return view('admin.mechanic-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMechanicAd $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMechanicAd $request)
    {
        try {

            $sanitized = $request->getSanitized();

            $slug = $this->slugAd($sanitized['title']);

            $ad = Ad::create([
                'slug' => $slug,
                'title' => $sanitized['title'],
                'description' => $sanitized['description'],
               // 'thumbnail' => $sanitized['thumbnail'],
                'status' => 0,
                'type' => 'mechanic',
                'is_featured' => 0,
                'user_id' => Auth::user()->id,
                'market_id' => $sanitized['market_id'],
                'external_id' =>null,
                'source' => null,
                'images_processing_status' => 'SUCCESSFUL',
                'images_processing_status_text' => null,
            ]);
            
            $thumbnail = '';
            $i = 0;
            if ($request->file()) {
                foreach ($request->file() as $file) {
                    if ($i == 0) {
                        $thumbnail = $this->uploadFile($file,$ad->id,$i);
                    }else{
                        $this->uploadFile($file,$ad->id,$i);
                    }
                    $i++;
                }
            }

            $mechanicAd = MechanicAd::create([
                'ad_id' =>  $ad->id,
                'address' => $sanitized['address'],
                'latitude' => $sanitized['latitude'] ?? null,
                'longitude' => $sanitized['longitude'] ?? null,
                'zip_code' => $sanitized['zip_code'],
                'city' => $sanitized['city'],
                'country' =>$sanitized['country'],
                'mobile_number' => $sanitized['mobile_number'],
                'whatsapp_number' => $sanitized['whatsapp_number'],
                'website_url' => $sanitized['website_url'],
                'email_address' => $sanitized['email_address'],
                'geocoding_status' => $sanitized['geocoding_status'] ?? null
            ]);

            
            $ad->thumbnail = $thumbnail;
            $ad->save();

            $images = AdImage::where('ad_id',$ad->id)->get();

            return response()->json(['data' => ['ad' => $ad,'mechanic_ad' => $mechanicAd,'images' => $images]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }
        

    /**
     * Display the specified resource.
     *
     * @param MechanicAd $mechanicAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(MechanicAd $mechanicAd)
    {
        $this->authorize('admin.mechanic-ad.show', $mechanicAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MechanicAd $mechanicAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MechanicAd $mechanicAd)
    {
        $this->authorize('admin.mechanic-ad.edit', $mechanicAd);


        return view('admin.mechanic-ad.edit', [
            'mechanicAd' => $mechanicAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMechanicAd $request
     * @param MechanicAd $mechanicAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMechanicAd $request, $id)
    {
        try {

            $sanitized = $request->getSanitized();

            $slug = $this->slugAd($sanitized['title']);

            $ad = Ad::where('id',$id)->update([
                'slug' => $slug,
                'title' => $sanitized['title'],
                'description' => $sanitized['description'],
               // 'thumbnail' => $sanitized['thumbnail'],
                'status' => 0,
                'type' => 'mechanic',
                'is_featured' => 0,
                'user_id' => Auth::user()->id,
                'market_id' => $sanitized['market_id'],
                'external_id' =>null,
                'source' => null,
                'images_processing_status' => 'SUCCESSFUL',
                'images_processing_status_text' => null,
            ]);
            
            $thumbnail = '';
            $i = 0;
            if ($request->file()) {
                foreach ($request->file() as $file) {
                    if ($i == 0) {
                        $thumbnail = $this->uploadFile($file,$ad->id,$i);
                    }else{
                        $this->uploadFile($file,$ad->id,$i);
                    }
                    $i++;
                }
            }

            $mechanicAd = MechanicAd::where('ad_id',$id)->update([
                'ad_id' =>  $ad->id,
                'address' => $sanitized['address'],
                'latitude' => $sanitized['latitude'] ?? null,
                'longitude' => $sanitized['longitude'] ?? null,
                'zip_code' => $sanitized['zip_code'],
                'city' => $sanitized['city'],
                'country' =>$sanitized['country'],
                'mobile_number' => $sanitized['mobile_number'],
                'whatsapp_number' => $sanitized['whatsapp_number'],
                'website_url' => $sanitized['website_url'],
                'email_address' => $sanitized['email_address'],
                'geocoding_status' => $sanitized['geocoding_status'] ?? null
            ]);

            
            $thumbnail != '' ? Ad::where('id',$id)->update(['thumbnail' => $thumbnail]) : null;


            $images = AdImage::where('ad_id',$ad->id)->get();

            return response()->json(['data' => ['ad' => $ad,'mechanic_ad' => $mechanicAd,'images' => $images]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMechanicAd $request
     * @param MechanicAd $mechanicAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMechanicAd $request, MechanicAd $mechanicAd)
    {
        $mechanicAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMechanicAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMechanicAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    MechanicAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function uploadFile($file,$ad_id,$order_index)
    {   
        $path = null;
        
        if ($file) {
            $path = $file->store(
                'listings/'.$ad_id, 's3'
            );
        }
        
        AdImage::create([
            'ad_id' => $ad_id,
            'path' => $path, 
            'is_external' => 1, 
            'order_index' => $order_index
        ]);

        return $path;
    }

    public function calculaDistancia($longitud1, $latitud1, $longitud2, $latitud2){

       //calculamos la diferencia de entre la longitud de los dos puntos
       $diferenciaX = $longitud1 - $longitud2;

       //ahora calculamos la diferencia entre la latitud de los dos puntos
       $diferenciaY = $latitud1 -$latitud2;

       // ahora ponemos en practica el teorema de pitagora para calcular la distancia
       $distancia = sqrt(pow($diferenciaX,2) + pow($diferenciaY,2));
    }

    private function slugAd($title)
    {   
        $response = Str::slug($title);

        $validate = Ad::where('slug',Str::slug($title))->count();  
        
        if ($validate  != 0) {
            $response .= '-'.Str::uuid()->toString().'-'.$validate;
        }
        
        return $response;
    }

    public function mechanicAdsPromotedFrontPage(Request $request)
    {
        $data = Ad::whereRaw('id in(SELECT ad_id FROM promoted_front_page_ads)')->where('type','mechanic')->inRandomOrder()->limit(25);

        $data->with([
                        'images',
                        'mechanicAd'
                    ]
                );

        return ['data' => $data->get()];
    }

}
