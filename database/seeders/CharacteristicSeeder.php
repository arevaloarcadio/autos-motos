<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Characteristic;

class CharacteristicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Characteristic::create( [
		'id'=>'0fc5894e-a826-4fad-a787-1064aeffac33',
		'name'=>'Sistema de asistencia al aparcamiento',
		'created_at'=>'2022-06-28 10:28:50',
		'updated_at'=>'2022-06-28 10:28:50'
		] );
					
		Characteristic::create( [
		'id'=>'19a7de67-eea5-4268-b0aa-409a0a9ce75a',
		'name'=>'Adicional',
		'created_at'=>'2022-06-28 10:28:15',
		'updated_at'=>'2022-06-28 10:28:15'
		] );
					
		Characteristic::create( [
		'id'=>'26f93a11-e343-4181-9ad7-65c95d38ee72',
		'name'=>'Sport',
		'created_at'=>'2022-06-28 10:29:06',
		'updated_at'=>'2022-06-28 10:29:06'
		] );
					
		Characteristic::create( [
		'id'=>'3a9d1b80-3f01-4326-9d11-41920f8309a5',
		'name'=>'Ruedas y neumáticos',
		'created_at'=>'2022-06-28 10:29:22',
		'updated_at'=>'2022-06-28 10:29:22'
		] );
					
		Characteristic::create( [
		'id'=>'570e120f-4ecf-47f0-9443-186da242eada',
		'name'=>'Seguridad',
		'created_at'=>'2022-06-28 10:28:59',
		'updated_at'=>'2022-06-28 10:28:59'
		] );
					
		Characteristic::create( [
		'id'=>'6d3b54a6-6569-4b1c-9237-89a8e70459f4',
		'name'=>'Comfort',
		'created_at'=>'2022-06-28 10:28:02',
		'updated_at'=>'2022-06-28 10:28:02'
		] );
					
		Characteristic::create( [
		'id'=>'dd3b5f32-77d5-41db-9f5f-69fbe588d8af',
		'name'=>'Asistencia de frenado',
		'created_at'=>'2022-06-28 10:27:18',
		'updated_at'=>'2022-06-28 10:27:18'
		] );
					
		Characteristic::create( [
		'id'=>'ef2a1161-5ee6-496f-ab50-c2ad9ca09813',
		'name'=>'Material interior',
		'created_at'=>'2022-06-28 10:28:33',
		'updated_at'=>'2022-06-28 10:28:33'
		] );
					
		Characteristic::create( [
		'id'=>'f458fd73-03a5-4de3-a055-e05a0d22bee6',
		'name'=>'Faros',
		'created_at'=>'2022-06-28 10:28:25',
		'updated_at'=>'2022-06-28 10:28:25'
		] );
    }
}
