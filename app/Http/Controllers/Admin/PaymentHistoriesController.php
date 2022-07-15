<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentHistory\BulkDestroyPaymentHistory;
use App\Http\Requests\Admin\PaymentHistory\DestroyPaymentHistory;
use App\Http\Requests\Admin\PaymentHistory\IndexPaymentHistory;
use App\Http\Requests\Admin\PaymentHistory\StorePaymentHistory;
use App\Http\Requests\Admin\PaymentHistory\UpdatePaymentHistory;
use App\Models\PaymentHistory;
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

use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentHistoriesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexPaymentHistory $request
     * @return array|Factory|View
     */
    public function index(IndexPaymentHistory $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(PaymentHistory::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'mount', 'status', 'way_to_pay','user_id', 'transaction_number'],

            // set columns to searchIn
            ['id', 'status', 'way_to_pay','user_id',]
        );

        return ['data' => $data];

    }


    public function byUser(Request $request)
    {   
        
        $resource = ApiHelper::resource();
    
        try {
            
            $payment_histories = PaymentHistory::select('payment_histories.*')
                ->join('users','users.id','payment_histories.user_id')
                ->where('payment_histories.user_id',Auth::user()->id);
                
              
            return response()->json(['data' => $payment_histories->get()], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }


     public function byUserAdmin(Request $request,$id)
    {   
        
        $resource = ApiHelper::resource();
    
        try {
            
            $payment_histories = PaymentHistory::select('payment_histories.*')
                ->join('users','users.id','payment_histories.user_id')
                ->where('payment_histories.user_id',$id);
                   
            return response()->json(['data' => $payment_histories->get()], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.payment-history.create');

        return view('admin.payment-history.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePaymentHistory $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StorePaymentHistory $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the PaymentHistory
        $paymentHistory = PaymentHistory::create($sanitized);

        return ['data' => $paymentHistory];
    }

    /**
     * Display the specified resource.
     *
     * @param PaymentHistory $paymentHistory
     * @throws AuthorizationException
     * @return void
     */
    public function show(PaymentHistory $paymentHistory)
    {
        $this->authorize('admin.payment-history.show', $paymentHistory);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PaymentHistory $paymentHistory
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(PaymentHistory $paymentHistory)
    {
        $this->authorize('admin.payment-history.edit', $paymentHistory);


        return view('admin.payment-history.edit', [
            'paymentHistory' => $paymentHistory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePaymentHistory $request
     * @param PaymentHistory $paymentHistory
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdatePaymentHistory $request, PaymentHistory $paymentHistory)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values PaymentHistory
        $paymentHistory->update($sanitized);

        return ['data' => $paymentHistory];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyPaymentHistory $request
     * @param PaymentHistory $paymentHistory
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyPaymentHistory $request, PaymentHistory $paymentHistory)
    {
        $paymentHistory->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyPaymentHistory $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyPaymentHistory $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    PaymentHistory::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
