<?php

namespace App\Http\Controllers\Admin;

use App\Models\Models;
use App\Models\Submodel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubModel\StoreRequest;
use App\Http\Requests\Admin\SubModel\UpdateRequest;
use App\Http\Requests\Admin\SubModel\UpdateModelsRequest;

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
    public function store(StoreRequest $request)
    {
        $data = $request->all();
        $sub_model_exist = Submodel::where('name',$data['name'])->where('make_id',$data['make_id'])->first();
        if($sub_model_exist){
            return response()->json([
                'data' => [],
                'message' => 'Ya existe un grupo con este nombre para esta marca',
                'ok' => false
            ],400);
        }
        $sub_model = new Submodel;
        $sub_model->name = $data['name'];
        $sub_model->make_id = $data['make_id'];
        if(isset($data['value'])){
            $sub_model->value = $data['value'];
        }
        $sub_model->save();
        return response()->json([
            'data' => $sub_model,
            'message' => 'Grupo creado',
            'ok' => true
        ],201);
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
    public function update(UpdateRequest $request, Submodel $sub_model)
    {
        $data = $request->all();
        $sub_model_exist = Submodel::where('name',$data['name'])->where('make_id',$data['make_id'])->where('id','!=',$sub_model->id)->first();
        if($sub_model_exist){
            return response()->json([
                'data' => [],
                'message' => 'Ya existe un grupo con este nombre para esta marca',
                'ok' => false
            ],400);
        }
        $sub_model->name = $data['name'];
        $sub_model->make_id = $data['make_id'];
        if(isset($data['value'])){
            $sub_model->value = $data['value'];
        }
        $sub_model->save();
        return response()->json([
            'data' => $sub_model,
            'message' => 'Grupo actualizado',
            'ok' => true
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Submodel  $submodel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Submodel $sub_model)
    {
        $submodel_id = $sub_model->id;
        $submodel->models()->update(['sub_model_id' => null]);
        $submodel->delete();
        return response()->json([
            'data'=> [],
            'ok' => true
        ],200);
    }

    public function updateModels(UpdateModelsRequest $request, Submodel $sub_model)
    {
        $old_models = $sub_model->models()->get()->pluck('id')->toArray();
        $models = $request->models;
        $deleted_models = array_diff($old_models,$models);
        if ($deleted_models) {
            Models::whereIn('id',$deleted_models)->update([
                'sub_model_id' => null
            ]);
        }
        Models::whereIn('id',$models)->update([
            'sub_model_id' => $sub_model->id
        ]);
        return response()->json([
            'data'=> [],
            'message'=> 'Modelos de grupo actualizados',
            'ok' => true
        ],200);
    }
}
