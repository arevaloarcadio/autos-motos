<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Plan,ItemPlan,CharacteristicPlan,CharacteristicPromotionPlan};
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
        
        CharacteristicPlan::create( [
        'id'=>Str::uuid()->toString(),
        'front_page_promotion'=>10,
        'video_a_day'=>0,
        'vehicle_ads'=>45,
        'shop_ads'=>20,
        'rental_ads'=>0,
        'mechanic_ads'=>0,
        'plan_id'=>'5a570926-8794-436a-bc00-bc51094154b0',
        'created_at'=>'2022-07-27 04:16:39',
        'updated_at'=>'2022-07-27 04:16:39'
        ] );

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

        CharacteristicPlan::create( [
        'id'=>Str::uuid()->toString(),
        'front_page_promotion'=>25,
        'video_a_day'=>0,
        'vehicle_ads'=>75,
        'shop_ads'=>40,
        'rental_ads'=>0,
        'mechanic_ads'=>0,
        'plan_id'=>$plan->id,
        'created_at'=>'2022-07-27 04:21:43',
        'updated_at'=>'2022-07-27 04:21:43'
        ] );

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

		
        CharacteristicPlan::create( [
        'id'=>Str::uuid()->toString(),
        'front_page_promotion'=>50,
        'video_a_day'=>1,
        'vehicle_ads'=>100,
        'shop_ads'=>100,
        'rental_ads'=>9999,
        'mechanic_ads'=>9999,
        'plan_id'=>$plan->id,
        'created_at'=>'2022-07-27 04:24:20',
        'updated_at'=>'2022-07-27 04:24:20'
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

        CharacteristicPromotionPlan::create( [
        'id'=>Str::uuid()->toString(),
        'vehicle_ads'=>1,
        'shop_ads'=>0,
        'rental_ads'=>0,
        'mechanic_ads'=>0,
        'front_page_promotion'=>0,
        'plan_id'=>$plan->id,
        'created_at'=>'2022-07-27 04:34:10',
        'updated_at'=>'2022-07-27 04:34:10'
        ] );

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

        CharacteristicPromotionPlan::create( [
        'id'=>Str::uuid()->toString(),
        'vehicle_ads'=>0,
        'shop_ads'=>1,
        'rental_ads'=>0,
        'mechanic_ads'=>0,
        'front_page_promotion'=>0,
        'plan_id'=>$plan->id,
        'created_at'=>'2022-07-27 04:33:29',
        'updated_at'=>'2022-07-27 04:33:29'
        ] );


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

        CharacteristicPromotionPlan::create( [
        'id'=>Str::uuid()->toString(),
        'vehicle_ads'=>0,
        'shop_ads'=>0,
        'rental_ads'=>1,
        'mechanic_ads'=>0,
        'front_page_promotion'=>0,
        'plan_id'=>$plan->id,
        'created_at'=>'2022-07-27 04:29:09',
        'updated_at'=>'2022-07-27 04:29:09'
        ] );

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

        CharacteristicPromotionPlan::create( [
        'id'=>Str::uuid()->toString(),
        'vehicle_ads'=>0,
        'shop_ads'=>0,
        'rental_ads'=>0,
        'mechanic_ads'=>1,
        'front_page_promotion'=>0,
        'plan_id'=>$plan->id,
        'created_at'=>'2022-07-27 04:35:44',
        'updated_at'=>'2022-07-27 04:35:44'
        ] );


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

        CharacteristicPromotionPlan::create( [
        'id'=>Str::uuid()->toString(),
        'vehicle_ads'=>0,
        'shop_ads'=>0,
        'rental_ads'=>0,
        'mechanic_ads'=>0,
        'front_page_promotion'=>1,
        'plan_id'=>$plan->id,
        'created_at'=>'2022-07-27 04:34:53',
        'updated_at'=>'2022-07-27 04:34:53'
        ] );

		ItemPlan::create([
        	'id'=> Str::uuid()->toString(),
        	'plan_id'=> $plan->id,
        	'data'=>'1 anuncio promocionado en la primera página',
        	'created_at'=>'2022-06-28 10:28:50',
			'updated_at'=>'2022-06-28 10:28:50'
        ]);
    }
}
