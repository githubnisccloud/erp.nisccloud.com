<?php

namespace Modules\Training\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Training';
        $menu = $event->menu;
        $menu->add([
            'title' => 'Training',
            'icon' => '',
            'name' => 'training',
            'parent' => 'hrm',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'trainings manage'
        ]);
        $menu->add([
            'title' => 'Training List',
            'icon' => '',
            'name' => 'training-list',
            'parent' => 'training',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'training.index',
            'module' => $module,
            'permission' => 'training manage'
        ]);
        $menu->add([
            'title' => 'Trainer',
            'icon' => '',
            'name' => 'trainer',
            'parent' => 'training',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'trainer.index',
            'module' => $module,
            'permission' => 'trainer manage'
        ]);
    }
}
