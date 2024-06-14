<?php

namespace Modules\Spreadsheet\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Spreadsheet\Entities\Related;
use Nwidart\Modules\Facades\Module;

class RelatedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this_module = Module::find('Spreadsheet');
        $this_module->enable();

        $sub_module = [
            [
                'related' => 'Project',
                'model_name' => 'Project',
            ],
            [
                'related' => 'Contract',
                'model_name' => 'Contract',
            ],
            [
                'related' => 'Lead',
                'model_name' => 'Lead',
            ],
            [
                'related' => 'Deal',
                'model_name' => 'Deal',
            ],
        ];

        foreach ($sub_module as $sm) {
            $check = Related::where('related', $sm['related'])->where('model_name', $sm['model_name'])->first();

            if (!$check) {
                Related::create([
                    'related' => $sm['related'],
                    'model_name' => $sm['model_name'],
                ]);
            }
        }
    }
}
