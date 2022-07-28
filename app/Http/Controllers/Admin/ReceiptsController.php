<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Receipt\BulkDestroyReceipt;
use App\Http\Requests\Admin\Receipt\DestroyReceipt;
use App\Http\Requests\Admin\Receipt\IndexReceipt;
use App\Http\Requests\Admin\Receipt\StoreReceipt;
use App\Http\Requests\Admin\Receipt\UpdateReceipt;
use App\Models\{UserPlan,Plan,PaymentHistory,Receipt};
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Illuminate\Http\Request;
use App\Http\Resources\Data;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReceiptsController extends Controller
{
    use ApiController;

    /**
     * Display a listing of the resource.
     *
     * @param IndexReceipt $request
     * @return array|Factory|View
     */
    public function index(IndexReceipt $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Receipt::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'plan_id', 'user_id', 'file'],

            // set columns to searchIn
            ['id', 'plan_id', 'user_id', 'file'],
            function ($query) use ($request){
                foreach (Receipt::getRelationships() as $key => $value) {
                   $query->with($key);
                }
            }
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
        $this->authorize('admin.receipt.create');

        return view('admin.receipt.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreReceipt $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreReceipt $request)
    {
        $resource = ApiHelper::resource();
        
        $sanitized = $request->getSanitized();
        
        try {

            $data = [];
            
            $user_id = Auth::user()->id;
            $plan = Plan::find($sanitized['plan_id']);

            $data['user_id'] = $user_id;
            $data['plan_id'] = $sanitized['plan_id'];
            /*$data['name'] = $sanitized['name'];
            $data['email'] = $sanitized['email'];
            $data['phone'] = $sanitized['phone'];
            $data['country'] = $sanitized['country'];*/
            $data['file'] = $this->uploadFile($request->file('file'),$user_id);


            $receipt = Receipt::create($data);    
            
            $user_plan = new UserPlan;
            $user_plan->user_id = $user_id;
            $user_plan->plan_id = $sanitized['plan_id'];
            $user_plan->status = 'Pendiente';
            //$user_plan->date_end_at = Carbon::now()->addDays(30);
            
            $user_plan->save();
            
            $transaction_number = PaymentHistory::count();    
            
            $payment_history = new PaymentHistory;
            $payment_history->mount =  $plan->price;
            $payment_history->status = 'Pendiente';
            $payment_history->user_id = $user_id;
            $payment_history->way_to_pay = 'Transferencia Bancaria';
            $payment_history->transaction_number = str_pad($transaction_number,11,"0",STR_PAD_LEFT);

            $payment_history->save();


            return response()->json(['data' => $receipt], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Receipt $receipt
     * @throws AuthorizationException
     * @return void
     */
    public function show(Receipt $receipt)
    {
        $this->authorize('admin.receipt.show', $receipt);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Receipt $receipt
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Receipt $receipt)
    {
        $this->authorize('admin.receipt.edit', $receipt);


        return view('admin.receipt.edit', [
            'receipt' => $receipt,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateReceipt $request
     * @param Receipt $receipt
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateReceipt $request, Receipt $receipt)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Receipt
        $receipt->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/receipts'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/receipts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyReceipt $request
     * @param Receipt $receipt
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyReceipt $request, Receipt $receipt)
    {
        $receipt->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyReceipt $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyReceipt $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Receipt::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function uploadFile($file,$id)
    {   
        $path = null;
        
        if ($file) {
            $path = $file->store(
                'receipts/'.$id, 's3'
            );
        }
        
        return $path;
    }
}
