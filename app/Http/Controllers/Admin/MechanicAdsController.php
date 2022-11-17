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
use Illuminate\Support\Facades\Redis as Redis;
use Illuminate\Support\Arr;
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
        // $querys =$request->query->get();

        if(
            Redis::exists('mechanic_ads') &&
            !$request->filters &&
            $request->query->get('orderBy') == 'created_at'  &&
            $request->query->get('orderDirection') == 'desc'
        ) {
            $data = json_decode(Redis::get('mechanic_ads'));
            return ['data' => $data];
        }
        $request['per_page'] = isset($request->per_page) ? $request->per_page : 30;
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
                                if ($key == 'city') {
                                    $query->where($key,'LIKE', '%'.$filter.'%');
                                }else{
                                   $query->where($key,$filter);
                                }

                            }
                        }
                    }
                }

                $query->whereRaw('ad_id in(SELECT id FROM ads WHERE status = 10 and thumbnail is not null)');


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
                    $query->with(['images','user']);
                }]);
            }
        );


        if(
            !$request->filters &&
            $request->query->get('orderBy') == 'created_at'  &&
            $request->query->get('orderDirection') == 'desc'
        ){
            Redis::set('mechanic_ads',json_encode($data ));
        }

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

            $file = $request->file('images');
            $thumbnail = $this->uploadFile($file,$ad->id,0,true);
            $ad->thumbnail = $thumbnail;
            $ad->save();

            $dealer_show_room_id = Auth::user()->dealer_id !== null ? DealerShowRoom::where('dealer_id',Auth::user()->dealer_id)->first()['id'] : null;
            Redis::del('mechanic_ads');
            Redis::del('by_user_'.Auth::user()->id.'_filter_mechanic');
            $mechanicAd = MechanicAd::create([
                'ad_id' =>  $ad->id,
                'address' => $sanitized['address'],
                'latitude' => $sanitized['latitude'] ?? null,
                'longitude' => $sanitized['longitude'] ?? null,
                'zip_code' => $sanitized['zip_code'],
                'city' => $sanitized['city'],
                'country' =>$sanitized['country'],
                'dealer_id' => Auth::user()->dealer_id ?? null,
                'dealer_show_room_id' => $dealer_show_room_id,
                'mobile_number' => $sanitized['mobile_number'],
                'whatsapp_number' => $sanitized['whatsapp_number'],
                'country_code_mobile_number' => $sanitized['country_code_mobile_number'],
                'country_code_whatsapp_number' => $sanitized['country_code_whatsapp_number'],
                'website_url' => $sanitized['website_url'],
                'email_address' => $sanitized['email_address'],
                'geocoding_status' => $sanitized['geocoding_status'] ?? null
            ]);

            $user = Auth::user();

            $user->notify(new \App\Notifications\NewAd($user));

            return response()->json(['data' => ['ad' => $ad,'mechanic_ad' => $mechanicAd]], 200);

        } catch (Exception $e) {
            $ad->delete();
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

            $ad =  Ad::where('id',$id)->first();
            $ad->title =  $sanitized['title'];
            $ad->description =  $sanitized['description'];
            $ad->status =  0;
            $ad->save();

            $thumbnail = '';
            $i = 0;

            if ($request->file('images')) {
                $file = $request->file('images');
                $thumbnail = $this->uploadFile($file,$ad->id,0,true);
                $ad->thumbnail = $thumbnail;
                $ad->save();
            }

            $mechanicAd = MechanicAd::where('ad_id',$id)->update([
                'address' => $sanitized['address'],
                'latitude' => $sanitized['latitude'] ?? null,
                'longitude' => $sanitized['longitude'] ?? null,
                'zip_code' => $sanitized['zip_code'],
                'city' => $sanitized['city'],
                'country' => $sanitized['country'],
                'mobile_number' => $sanitized['mobile_number'],
                'whatsapp_number' => $sanitized['whatsapp_number'],
                'country_code_mobile_number' => $sanitized['country_code_mobile_number'],
                'country_code_whatsapp_number' => $sanitized['country_code_whatsapp_number'],
                'website_url' => $sanitized['website_url'],
                'email_address' => $sanitized['email_address'],
                'geocoding_status' => $sanitized['geocoding_status'] ?? null
            ]);

            $mechanicAd = MechanicAd::where('ad_id',$id)->first();

            Redis::del('mechanic_ads');
            Redis::del('by_user_'.Auth::user()->id.'_filter_mechanic');

            return response()->json(['data' => ['ad' => $ad,'mechanic_ad' => $mechanicAd]], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage().', Line '.$e->getLine());
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
        Redis::del('mechanic_ads');
        Redis::del('by_user_'.Auth::user()->id.'_filter_mechanic');
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

    public function uploadFile($file,$ad_id,$order_index,$thumbnail = false)
    {
        $path = null;

        if ($file) {
            $path = $file->store(
                'listings/'.$ad_id, 's3'
            );
        }

        if (!$thumbnail) {
           AdImage::create([
                'ad_id' => $ad_id,
                'path' => $path,
                'is_external' => 1,
                'order_index' => $order_index
            ]);
        }

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
                        'mechanicAd' => function($query)
                        {
                            $query->with(['dealer','dealerShowRoom']);
                        }
                    ]
                );

        return ['data' => $data->get()];
    }

}
