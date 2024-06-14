<?php

namespace Modules\VCard\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'VCard';
        $menu = $event->menu;
        $menu->add([
            'title' => 'vCard Dashboard',
            'icon' => '',
            'name' => 'vcard-dashboard',
            'parent' => 'dashboard',
            'order' => 70,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'dashboard.vcard',
            'module' => $module,
            'permission' => 'vcard dashboard manage'
        ]);
        $menu->add([
            'title' => __('vCard'),
            'icon' => 'credit-card',
            'name' => 'vcard',
            'parent' => null,
            'order' => 550,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'vcard manage'
        ]);
        $menu->add([
            'title' => __('Business'),
            'icon' => '',
            'name' => 'business',
            'parent' => 'vcard',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'business.index',
            'module' => $module,
            'permission' => 'business manage'
        ]);
        $menu->add([
            'title' => __('Appointment'),
            'icon' => '',
            'name' => 'vcard-appointment',
            'parent' => 'vcard',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'appointment.index',
            'module' => $module,
            'permission' => 'card appointment manage'
        ]);
        $menu->add([
            'title' => __('Contact'),
            'icon' => '',
            'name' => 'contact',
            'parent' => 'vcard',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'contacts.index',
            'module' => $module,
            'permission' => 'card contact manage'
        ]);
    }
}
