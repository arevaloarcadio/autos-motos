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

		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Premium +',
			'price'=> 69.99,
			'type'=>'Profesional',
			'duration'=> 30,
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

		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Anuncio de recambios',
			'price'=> 0.99,
			'type'=>'Ocasional',
			'duration'=> 30,
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

		$plan = Plan::create([
			'id'=> Str::uuid()->toString(),
			'name'=>'Anuncio de talleres',
			'price'=> 2.99,
			'type'=>'Ocasional',
			'duration'=> 30,
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
    }
}
