<?php

namespace Modules\VideoHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoHubModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'module',
        'sub_module',
        'field_json',
    ];

    protected static function newFactory()
    {
        return \Modules\VideoHub\Database\factories\VideoHubModuleFactory::new();
    }

    // Get filter Module
    public static function filter($sub_module='')
    {
        $video_module   = VideoHubModule::where('sub_module',$sub_module)->first();
        $item_id        =  \Request::segment(2);
        if($video_module != null){
            $module = [
                'sub_module' => $video_module->id,
                'filter' => $video_module->module
            ];
            $video_subs = VideoHubVideo::where('sub_module_id',['sub_module' => $video_module->id])->get();

            foreach ($video_subs as $video_sub) {
                if ($video_sub->item_id == $item_id) {
                    $module = [
                        'item'          => (int)$video_sub->item_id,
                        'sub_module'    => $video_module->id,
                        'filter'        => $video_module->module];
                } else {
                    $module = [
                        'item'          => (isset($item_id) ? (int)$item_id : ''),
                        'sub_module'    => (isset($video_module->id) ? $video_module->id : 0),
                        'filter'        => $video_module->module
                    ];
                }
            }
            return $module;
        }else{
            $module = [
                'filter' => $sub_module
            ];
            $video_subs = VideoHubVideo::where('module',['filter' => $sub_module])->get();
            foreach ($video_subs as $video_sub) {
                if ($video_sub->item_id == $item_id) {
                    $module = [
                        'item'      => (int)$video_sub->item_id,
                        'filter'    => $sub_module];
                } else {
                    $module = [
                        'item'      => (isset($item_id) ? (int)$item_id : ''),
                        'filter'    => $sub_module];
                }
            }
            return $module;
        }
    }

    public static function get_view_to_stack_hook()
    {

        $views = [
            'Project'            => 'taskly::projects.show',    //show
            'Lead'               => 'lead::leads.show', //show
            'Deal'               => 'lead::deals.show', //show
        ];

        return $views;
    }
}
