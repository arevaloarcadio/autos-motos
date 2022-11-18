<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Models;
use App\Models\{Submodel,Make,SubmodelByModel};
use App\Models\Mark;

class ModelController extends Controller
{
    public function index()
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
         try {
                foreach ($request->marks as $mark) {
                    $mark['created_at'] = date('Y-m-d h:m');
                    $mark['updated_at'] = date('Y-m-d h:m');
                    Mark::insert($mark);
                }
              

            return response()->json(['response' => true], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function generadorYOrdenamiento(Request $request)
    {
        $modelos = Models::where('make_id',$request->make_id)->get();
        $grupo= Submodel::where('make_id',$request->make_id)->where('name','Otros')->first();
            foreach ($modelos as $model) {
                $modeloByGrupo=SubmodelByModel::where('model_id',$model->id)->first();
                if (!$modeloByGrupo) {
                    SubmodelByModel::create([
                        'model_id' =>  $model->id,
                        'sub_model_id' =>  $grupo->id,
                     ]);   
                }
            }
    }
}
