<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Review\BulkDestroyReview;
use App\Http\Requests\Admin\Review\DestroyReview;
use App\Http\Requests\Admin\Review\IndexReview;
use App\Http\Requests\Admin\Review\StoreReview;
use App\Http\Requests\Admin\Review\UpdateReview;
use App\Models\Review;
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

class ReviewsController extends Controller
{

    use  ApiController;
    /**
     * Display a listing of the resource.
     *
     * @param IndexReview $request
     * @return array|Factory|View
     */
    public function index(IndexReview $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Review::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'ad_id', 'name','testimony', 'score'],

            // set columns to searchIn
            ['id', 'ad_id', 'testimony', 'name', 'score'],

            function ($query) use ($request) {
                        
                $columns = ['id', 'ad_id', 'testimony', 'name', 'score'];
                
                if ($request->filters) {
                    foreach ($columns as $column) {
                        foreach ($request->filters as $key => $filter) {
                            if ($column == $key) {
                               $query->where($key,$filter);
                            }
                        }
                    }
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
        $this->authorize('admin.review.create');

        return view('admin.review.create');
    }

    
    public function byUser(Request $request)
    {   
        
        $resource = ApiHelper::resource();
    
        try {
            
            $reviews = Review::select('reviews.*')
                ->join('ads','ads.id','reviews.ad_id')
                ->where('ads.user_id',Auth::user()->id);
                
            if ($request->type) {
                if ($request->type == 'auto') {
                    $reviews->whereIn('ads.type',['auto','moto','mobile-home','truck']);
                }else{
                    $reviews->where('ads.type',$request->type);
                }
            }       
                   
            return response()->json(['data' => $reviews->get()], 200);

        } catch (Exception $e) {
            ApiHelper::setError($resource, 0, 500, $e->getMessage());
            return $this->sendResponse($resource);
        }
    }

    public function store(StoreReview $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Review
        $review = Review::create($sanitized);

        return ['data' => $review];
    }

    /**
     * Display the specified resource.
     *
     * @param Review $review
     * @throws AuthorizationException
     * @return void
     */
    public function show(Review $review)
    {
        $this->authorize('admin.review.show', $review);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Review $review
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Review $review)
    {
        $this->authorize('admin.review.edit', $review);


        return view('admin.review.edit', [
            'review' => $review,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateReview $request
     * @param Review $review
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateReview $request, Review $review)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Review
        $review->update($sanitized);

        return ['data' => $review];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyReview $request
     * @param Review $review
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyReview $request, Review $review)
    {
        $review->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyReview $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyReview $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Review::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
