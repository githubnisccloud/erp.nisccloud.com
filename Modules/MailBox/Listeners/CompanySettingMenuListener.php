<?php

namespace Modules\MailBox\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        
        $module = 'MailBox';
        $menu = $event->menu;
        $menu->add([
            'title' => 'EMail Box',
            'name' => 'mailbox',
            'order' => 730,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'mailbox-sidenav',
            'module' => $module,
            'permission' => 'mailbox manage'
        ]);
    }
}
