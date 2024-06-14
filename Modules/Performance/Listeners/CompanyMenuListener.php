<?php

namespace Modules\Performance\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Performance';
        $menu = $event->menu;
        $menu->add([
            'title' => 'Performance',
            'icon' => '',
            'name' => 'performance',
            'parent' => 'hrm',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'performance manage'
        ]);
        $menu->add([
            'title' => 'Indicator',
            'icon' => '',
            'name' => 'indicator',
            'parent' => 'performance',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'indicator.index',
            'module' => $module,
            'permission' => 'indicator manage'
        ]);
        $menu->add([
            'title' => 'Appraisal',
            'icon' => '',
            'name' => 'appraisal',
            'parent' => 'performance',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'appraisal.index',
            'module' => $module,
            'permission' => 'appraisal manage'
        ]);
        $menu->add([
            'title' => 'Goal Tracking',
            'icon' => '',
            'name' => 'goaltracking',
            'parent' => 'performance',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'goaltracking.index',
            'module' => $module,
            'permission' => 'goaltracking manage'
        ]);
    }
}
