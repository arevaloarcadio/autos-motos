<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submodel;
use Illuminate\Http\Request;

class SubModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Submodel  $submodel
     * @return \Illuminate\Http\Response
     */
    public function show(Submodel $submodel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Submodel  $submodel
     * @return \Illuminate\Http\Response
     */
    public function edit(Submodel $submodel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Submodel  $submodel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Submodel $sub_model)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Submodel  $submodel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Submodel $sub_model)
    {
        $submodel_id = $submodel->id;
        $submodel->models()->update(['sub_model_id' => null]);
        $submodel->delete();
        return response()->json([
            'data'=> [],
            'ok' => true
        ],200);
    }
}
