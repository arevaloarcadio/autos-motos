<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Plan,ItemPlan};
use Illuminate\Support\Str;


class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Basico',
			'price'=> 39.99,
			'type'=>'Profesional',
			'duration'=> 30,
			'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
		]);
        
        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'45 anuncios',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'10 promocionados en la primera página',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'20 anuncios de recambio',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

		
		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Premium',
			'price'=> 49.99,
			'type'=>'Profesional',
			'duration'=> 30,
			'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
		]);

		ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'75 anuncios',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'25 promocionados en la primera página',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'40 anuncios de recambio',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Premium +',
			'price'=> 69.99,
			'type'=>'Profesional',
			'duration'=> 30,
			'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
		]);

		

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'100 anuncios',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'50 promocionados en la primera página',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'100 anuncios de recambio',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'1 video por día',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

        ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'Publicación de anuncios de talleres y alquiler ',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Anuncio de coche',
			'price'=> 1.99,
			'type'=>'Ocasional',
			'duration'=> 30,
			'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
		]);

		ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'1 anuncio promocionado de venta de vehículos',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Anuncio de recambios',
			'price'=> 0.99,
			'type'=>'Ocasional',
			'duration'=> 30,
			'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
		]);

		ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'1 anuncio promocionado de venta de recambios',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);


		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Anuncio de alquiler',
			'price'=> 2.99,
			'type'=>'Ocasional',
			'duration'=> 30,
			'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
		]);

		ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'1 anuncio promocionado de alquiler',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Anuncio de talleres',
			'price'=> 2.99,
			'type'=>'Ocasional',
			'duration'=> 30,
			'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
		]);

		ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'1 anuncio promocionado de talleres',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);

		$plan =Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Primera página',
			'price'=> 1.99,
			'type'=>'Ocasional',
			'duration'=> 30,
			'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
		]);

		ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'1 anuncio promocionado en la primera página',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);
    }
}
