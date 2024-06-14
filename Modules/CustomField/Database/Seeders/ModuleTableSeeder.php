<?php

namespace Modules\CustomField\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();


        $custom_fileds = [];

        $custom_fileds['Base'] =  ['Proposal','Invoice'];
        $custom_fileds['Account'] = ['Customer', 'Vendor', 'Bill'];
        $custom_fileds['Assets'] =  ['Assets'];
        $custom_fileds['Contract'] =  ['Contract'];
        $custom_fileds['Hrm'] =  ['Employee'];
        $custom_fileds['Lead'] =  ['Lead','Deal'];
        $custom_fileds['Performance'] =  ['Goal Tracking'];
        $custom_fileds['Pos'] =  ['Warehouse','Purchase'];
        $custom_fileds['ProductService'] =  ['Product & Service'];
        $custom_fileds['Retainer'] =  ['Retainer'];
        $custom_fileds['Rotas'] =  ['RotaEmployee'];
        $custom_fileds['Sales'] =  ['Quotes','Sales Invoice','Sales Order'];
        $custom_fileds['Taskly'] =  ['Projects','Tasks','Bugs'];


        foreach ($custom_fileds as $module => $custom_filed) {
                foreach ($custom_filed as $sm) {
                    $check = \Modules\CustomField\Entities\CustomFieldsModuleList::where('module', $module)->where('sub_module', $sm)->first();
                    if (!$check) {
                        $new = new \Modules\CustomField\Entities\CustomFieldsModuleList();
                        $new->module = $module;
                        $new->sub_module = $sm;
                        $new->save();
                    }
                }
        }

    }
}
