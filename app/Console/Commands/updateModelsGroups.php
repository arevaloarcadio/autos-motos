<?php

namespace App\Console\Commands;

use App\Models\Models;
use App\Models\SubmodelByModel;
use Illuminate\Console\Command;

class updateModelsGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'models_group:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sub_models = SubmodelByModel::all();
        $count = 0;
        foreach ($sub_models as $sub_model) {
            $model_id = $sub_model->model_id;
            $model = Models::find($model_id);
            $model->sub_model_id = $sub_model->sub_model_id;
            $model->save();
            $count++;
        }
        $this->line($count . " filas afectadas");
        return 0;
    }
}
