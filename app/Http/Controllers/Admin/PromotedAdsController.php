<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromotedAd\BulkDestroyPromotedAd;
use App\Http\Requests\Admin\PromotedAd\DestroyPromotedAd;
use App\Http\Requests\Admin\PromotedAd\IndexPromotedAd;
use App\Http\Requests\Admin\PromotedAd\StorePromotedAd;
use App\Http\Requests\Admin\PromotedAd\UpdatePromotedAd;
use App\Models\{PromotedFrontPageAd,PromotedSimpleAd,CharacteristicPlan,CharacteristicPromotionPlan,User,Plan,Ad};
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

class PromotedAdsController extends Controller
{

    use ApiController;
    /**
     * Display a listing of the resource.
     *
     * @param IndexPromotedAd $request
     * @return array|Factory|View
     */
    public function index(IndexPromotedAd $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(PromotedAd::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'user_id'],

            // set columns to searchIn
            ['id', 'ad_id', 'user_id']
        );

        return ['data' => $data];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.promoted-ad.create');

        return view('admin.promoted-ad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePromotedAd $request
     * @return array|RedirectResponse|Redirector
     */
     public function store(StorePromotedAd $request)
    {
        $sanitized = $request->getSanitized();
        
        $resource = ApiHelper::resource();

        try {
            
            $user = Auth::user();
            
            if (count($user->plan_active) == 0) {
                ApiHelper::setError($resource, 0, 422, ['data' => 'Actualmente no tiene plan activo para promocionar']);
                return $this->sendResponse($resource);
            }
            
            $ad = Ad::find($sanitized['ad_id']);

            $plan = Plan::find($user->plan_active->first()->id);
            
            if ($sanitized['type'] == 'front_page') {
                if ($user->type == 'Profesional') {
                   $validate = $this->getFrontPageCharacteristicPlan($plan); 
                }
                if ($user->type == 'Ocasional') {
                   $validate = $this->getFrontPageCharacteristicPromotionPlan($plan); 
                }
            }

            if ($sanitized['type'] == 'simple') {
                if ($user->type == 'Profesional') {

                   $validate = $this->getCharacteristicPlan($plan,$ad->type); 
                }
                if ($user->type == 'Ocasional') {
                   $validate = $this->getCharacteristicPromotionPlan($plan,$ad->type); 
                }
            }
            
            if (!$validate) {
                ApiHelper::setError($resource, 0, 422, ['data' => 'Ha alcanzado su límite para promocionar o no tiene plan para promociar ese tipo de anuncio']);
                return $this->sendResponse($resource);
            }

            $promoted_ad = null;
            
            if ($validate && $sanitized['type'] == 'simple') {
                $promoted_ad = new PromotedSimpleAd;
                $promoted_ad->ad_id = $sanitized['ad_id'];
                $promoted_ad->user_id = Auth::user()->id;
                $promoted_ad->save();
            }

            if ($validate && $sanitized['type'] == 'front_page') {
                $promoted_ad  = new PromotedFrontPageAd;
                $promoted_ad->ad_id = $sanitized['ad_id'];
                $promoted_ad->user_id = Auth::user()->id;
                $promoted_ad->save();
            }

            return response()->json(['data' => $promoted_ad], 200);
        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }
    
    public function getFrontPageCharacteristicPlan($plan)
    {
        $user_id = Auth::user()->id;

        $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();
       
        $promoted_front_page_ad = PromotedFrontPageAd::where('user_id',$user_id)->count();
       
        if (($characteristic['front_page_promotion'] - $promoted_front_page_ad) > 0) {
            return true;
        } 
        
        return false;
    }

    public function getFrontPageCharacteristicPromotionPlan($plan)
    {
        $user_id = Auth::user()->id;

        $characteristic = CharacteristicPromotionPlan::selectRaw('sum(front_page_promotion) as front_page_promotion')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user_id)
                    ->groupBy('front_page_promotion')
                    ->first();
       
        $promoted_front_page_ad = PromotedFrontPageAd::where('user_id',$user_id)->count();
       
        if (($characteristic['front_page_promotion'] - $promoted_front_page_ad) > 0) {
            return true;
        } 
        
        return false;
    }

    public function getCharacteristicPromotionPlan($plan,$ad_type)
    {
        $user_id = Auth::user()->id;
        $response = false;
        
        $promoted_front_page_ad = PromotedSimpleAd::where('user_id',$user_id)->count();
                
        switch ($ad_type) {
            case 'auto':
                
                $characteristic = CharacteristicPromotionPlan::selectRaw('sum(vehicle_ads) as vehicle_ads')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user_id)
                    ->groupBy('vehicle_ads')
                    ->first();
                
                if (($characteristic['vehicle_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                } 
                break;
            case 'moto':
                
                $characteristic = CharacteristicPromotionPlan::selectRaw('sum(vehicle_ads) as vehicle_ads')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user_id)
                    ->groupBy('vehicle_ads')
                    ->first();

                if (($characteristic['vehicle_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                } 
                break;
            case 'mobile-home':
                
                $characteristic = CharacteristicPromotionPlan::selectRaw('sum(vehicle_ads) as vehicle_ads')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user_id)
                    ->groupBy('vehicle_ads')
                    ->first();

                if (($characteristic['vehicle_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                } 
                break;
            case 'truck':

                $characteristic = CharacteristicPromotionPlan::selectRaw('sum(vehicle_ads) as vehicle_ads')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user_id)
                    ->groupBy('vehicle_ads')
                    ->first();

                if (($characteristic['vehicle_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                } 
                break;
            case 'shop':
                
                $characteristic = CharacteristicPromotionPlan::selectRaw('sum(shop_ads) as shop_ads')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user_id)
                    ->groupBy('shop_ads')
                    ->first();

                if (($characteristic['shop_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                }
                break;
            case 'mechanic':

                $characteristic = CharacteristicPromotionPlan::selectRaw('sum(mechanic_ads) as mechanic_ads')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user_id)
                    ->groupBy('mechanic_ads')
                    ->first();

                if (($characteristic['mechanic_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                }
                break;
            case 'rental':

                $characteristic = CharacteristicPromotionPlan::selectRaw('sum(rental_ads) as rental_ads')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user_id)
                    ->groupBy('rental_ads')
                    ->first();

                if (($characteristic['rental_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                }
                break;
            
            default:
                # code...
                break;
        }

        return $response;
    }


    public function getCharacteristicPlan($plan,$ad_type)
    {
        $user_id = Auth::user()->id;
        $response = false;
        
        $promoted_front_page_ad = PromotedSimpleAd::where('user_id',$user_id)->count();
        
        switch ($ad_type) {
            case 'auto':
                
                $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();
                
                if (($characteristic['vehicle_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                } 
                break;
            case 'moto':
                
                $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();

                if (($characteristic['vehicle_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                } 
                break;
            case 'mobile-home':
                
                $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();

                if (($characteristic['vehicle_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                } 
                break;
            case 'truck':

                $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();

                if (($characteristic['vehicle_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                } 
                break;
            case 'shop':
                
                $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();

                if (($characteristic['shop_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                }
                break;
            case 'mechanic':

                $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();

                if (($characteristic['mechanic_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                }
                break;
            case 'rental':

                $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();

                if (($characteristic['rental_ads'] - $promoted_front_page_ad) > 0) {
                    $response = true;
                }
                break;
            
            default:
                # code...
                break;
        }

        return $response;
    }

    
    public function getInfoPromoted(Request $request)
    {
        $user =  Auth::user();

        if ($user->type == 'Ocasional') {
            
            $characteristic = CharacteristicPromotionPlan::selectRaw('sum(rental_ads) as rental_ads , sum(mechanic_ads) as mechanic_ads,sum(vehicle_ads) as vehicle_ads,sum(shop_ads) as shop_ads,sum(front_page_promotion) as front_page_promotion')
                    ->join('user_plans','user_plans.plan_id','characteristic_promotion_plans.plan_id')
                    ->where('user_id',$user->id)
                    ->where('user_plans.status','Aprobado')
                    ->groupBy('rental_ads','mechanic_ads','vehicle_ads','shop_ads','front_page_promotion')
                    ->first();
            
            $count_promoted_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->count();

            $count_front_page_ads = Ad::join('promoted_front_page_ads','promoted_front_page_ads.ad_id','ads.id')
                ->where('promoted_front_page_ads.user_id',$user->id)
                ->count();

            $count_rental_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->where('ads.type','rental')
                ->count(); 

            $count_shop_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->where('ads.type','shop')
                ->count();

            $count_mechanic_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->where('ads.type','mechanic')
                ->count();

            $count_vehicle_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->whereIn('ads.type',['auto','moto','shop','truck'])
                ->count();

            $response = [
                'rental_ads' => [
                    'total' => $characteristic['rental_ads'] - $count_rental_ads,
                    'text' => 'Anuncios de alquiler restantes'
                ],
                'mechanic_ads' => [
                    'total' => $characteristic['mechanic_ads'] - $count_mechanic_ads,
                    'text' => 'Anuncios de taller restantes'
                ],
                'front_page_promotion' => [
                    'total' => $count_front_page_ads,
                    'text' => 'Anuncios en primera página'
                ],
                'front_page_promotion_rest' => [
                    'total' => $characteristic['front_page_promotion'] -$count_front_page_ads,
                    'text' => 'Anuncios en primera página restantes'
                ],
                'shop_ads' => [
                    'total' => $characteristic['shop_ads'] - $count_shop_ads,
                    'text' => 'Anuncios en recambio restantes'
                ],
                'vehicle_ads' => [
                    'total' => $characteristic['vehicle_ads'] - $count_vehicle_ads,
                    'text' => 'Anuncios restantes'
                ],
                'promoted_ads' => [
                    'total' => $count_promoted_ads,
                    'text' => 'Anuncios promocionados'
                ]
            ];

            return ['data' => $response];
        }
       
        if ($user->type == 'Profesional') {
            $plan = Plan::find($user->plan_active->first()->id);
            
            $characteristic = CharacteristicPlan::where('plan_id',$plan->id)->first();

            $count_promoted_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->count();

            $count_front_page_ads = Ad::join('promoted_front_page_ads','promoted_front_page_ads.ad_id','ads.id')
                ->where('promoted_front_page_ads.user_id',$user->id)
                ->count();

            $count_rental_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->where('ads.type','rental')
                ->count(); 

            $count_shop_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->where('ads.type','shop')
                ->count();

            $count_mechanic_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->where('ads.type','mechanic')
                ->count();

            $count_vehicle_ads = Ad::join('promoted_simple_ads','promoted_simple_ads.ad_id','ads.id')
                ->where('promoted_simple_ads.user_id',$user->id)
                ->whereIn('ads.type',['auto','moto','shop','truck'])
                ->count();
                dd($characteristic['vehicle_ads']. '-'. $count_vehicle_ads);
            $response = [
                'rental_ads' => [
                    'total' => $characteristic['rental_ads'] - $count_rental_ads,
                    'text' => 'Anuncios de alquiler restantes'
                ],
                'mechanic_ads' => [
                    'total' => $characteristic['mechanic_ads'] - $count_mechanic_ads,
                    'text' => 'Anuncios de taller restantes'
                ],
                'front_page_promotion' => [
                    'total' => $count_front_page_ads,
                    'text' => 'Anuncios en primera página'
                ],
                'front_page_promotion_rest' => [
                    'total' => $characteristic['front_page_promotion'] - $count_front_page_ads,
                    'text' => 'Anuncios en primera página restantes'
                ],
                'shop_ads' => [
                    'total' => $characteristic['shop_ads'] - $count_shop_ads,
                    'text' => 'Anuncios en recambio restantes'
                ],
                'vehicle_ads' => [
                    'total' => $characteristic['vehicle_ads'] - $count_vehicle_ads,
                    'text' => 'Anuncios restantes'
                ],
                'promoted_ads' => [
                    'total' => $count_promoted_ads,
                    'text' => 'Anuncios promocionados'
                ],
                'video_a_day' => [
                    'total' => $characteristic['video_a_day'],
                    'text' => 'Video por dia'
                ]
            ];

            return ['data' => $response];
           
        }
        
    }
    /**
     * Display the specified resource.
     *
     * @param PromotedAd $promotedAd
     * @throws AuthorizationException
     * @return void
     */
    public function show(PromotedAd $promotedAd)
    {
        $this->authorize('admin.promoted-ad.show', $promotedAd);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PromotedAd $promotedAd
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(PromotedAd $promotedAd)
    {
        $this->authorize('admin.promoted-ad.edit', $promotedAd);


        return view('admin.promoted-ad.edit', [
            'promotedAd' => $promotedAd,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePromotedAd $request
     * @param PromotedAd $promotedAd
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdatePromotedAd $request, PromotedAd $promotedAd)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values PromotedAd
        $promotedAd->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/promoted-ads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/promoted-ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyPromotedAd $request
     * @param PromotedAd $promotedAd
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyPromotedAd $request, PromotedAd $promotedAd)
    {
        $promotedAd->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyPromotedAd $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyPromotedAd $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    PromotedAd::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function deletePromoted(Request $request,$ad_id)
    {
       if ($request->type =='front_page') {
           PromotedFrontPageAd::where('ad_id',$ad_id)->delete();
       }
       if ($request->type =='simple') {
           PromotedSimpleAd::where('ad_id',$ad_id)->delete();
       }

       return ['data' => 'OK'];
    }
}
