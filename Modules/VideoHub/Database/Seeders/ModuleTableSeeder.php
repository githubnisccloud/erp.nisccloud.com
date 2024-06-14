<?php

namespace Modules\VideoHub\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\VideoHub\Entities\VideoHubModule;

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

        $sub_module = [
            'Lead', 'Deal'
        ];

        $content = [
            [
                'Lead' => '{"field":[{"label":"Lead","field_type":"select","field_name":"lead","placeholder":"Select Lead", "model_name": "Lead"}]}',
                'Deal' => '{"field":[{"label":"Deal","field_type":"select","field_name":"deal","placeholder":"Select Deal", "model_name": "Deal"}]}',
            ]
        ];
        foreach ($sub_module as $sm) {
            foreach ($content as $key => $item) {
                $check = VideoHubModule::where('module', 'CRM')->where('sub_module', $sm)->first();
                if (!$check) {
                    $new = new VideoHubModule();
                    $new->module = 'CRM';
                    $new->sub_module = $sm;
                    if ($sm == 'Lead') {
                        $new->field_json = $item[$sm];
                    }
                    if ($sm == 'Deal') {
                        $new->field_json = $item[$sm];
                    }
                    $new->save();
                }
            }
        }

        $sub_module = [
            ''
        ];

        $content = [
            [
                '' => '{"field":[{"label":"Project","field_type":"select","field_name":"project","placeholder":"Select Project", "model_name": "Project"}]}',
            ]
        ];
        foreach ($sub_module as $sm) {
            foreach ($content as $key => $item) {
                $check = VideoHubModule::where('module', 'Project')->where('sub_module', $sm)->first();
                if (!$check) {
                    $new = new VideoHubModule();
                    $new->module = 'Project';
                    $new->sub_module = $sm;
                    if ($sm == '') {
                        $new->field_json = $item[$sm];
                    }
                    $new->save();
                }
            }
        }

        $content = [
            [
                '' => '',
            ]
        ];
        foreach ($sub_module as $sm) {
            foreach ($content as $key => $item) {
                $check = VideoHubModule::where('module', 'vCard')->where('sub_module', $sm)->first();
                if (!$check) {
                    $new = new VideoHubModule();
                    $new->module = 'vCard';
                    $new->sub_module = $sm;
                    if ($sm == '') {
                        $new->field_json = $item[$sm];
                    }
                    $new->save();
                }
            }
        }

        foreach ($sub_module as $sm) {
            foreach ($content as $key => $item) {
                $check = VideoHubModule::where('module', 'Contract')->where('sub_module', $sm)->first();
                if (!$check) {
                    $new = new VideoHubModule();
                    $new->module = 'Contract';
                    $new->sub_module = $sm;
                    if ($sm == '') {
                        $new->field_json = $item[$sm];
                    }
                    $new->save();
                }
            }
        }

        foreach ($sub_module as $sm) {
            foreach ($content as $key => $item) {
                $check = VideoHubModule::where('module', 'Appointment')->where('sub_module', $sm)->first();
                if (!$check) {
                    $new = new VideoHubModule();
                    $new->module = 'Appointment';
                    $new->sub_module = $sm;
                    if ($sm == '') {
                        $new->field_json = $item[$sm];
                    }
                    $new->save();
                }
            }
        }

        foreach ($sub_module as $sm) {
            foreach ($content as $key => $item) {
                $check = VideoHubModule::where('module', 'Feedback')->where('sub_module', $sm)->first();
                if (!$check) {
                    $new = new VideoHubModule();
                    $new->module = 'Feedback';
                    $new->sub_module = $sm;
                    if ($sm == '') {
                        $new->field_json = $item[$sm];
                    }
                    $new->save();
                }
            }
        }
    }
}
