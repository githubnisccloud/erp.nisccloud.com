<?php

namespace Modules\Contract\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Contract';
        $menu = $event->menu;
        $menu->add([
            'title' => 'Contract',
            'icon' => 'device-floppy',
            'name' => 'contract',
            'parent' => null,
            'order' => 725,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'contract manage'
        ]);
        $menu->add([
            'title' => __('Contract'),
            'icon' => '',
            'name' => 'contract-menu',
            'parent' => 'contract',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'contract.index',
            'module' => $module,
            'permission' => 'contract manage'
        ]);
        $menu->add([
            'title' => __('Contract Type'),
            'icon' => '',
            'name' => 'contract-type',
            'parent' => 'contract',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'contract_type.index',
            'module' => $module,
            'permission' => 'contracttype manage'
        ]);
    }
}
