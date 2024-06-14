<?php

namespace Modules\DoubleEntry\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'DoubleEntry';
        $menu = $event->menu;
        $menu->add([
            'title' => __('DoubleEntry'),
            'icon' => 'scale',
            'name' => 'doubleentry',
            'parent' => null,
            'order' => 425,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'doubleentry manage'
        ]);
        $menu->add([
            'title' => __('Journal Account'),
            'icon' => '',
            'name' => 'journal-account',
            'parent' => 'doubleentry',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'journal-entry.index',
            'module' => $module,
            'permission' => 'journalentry manage'
        ]);
        $menu->add([
            'title' => __('Ledger Summary'),
            'icon' => '',
            'name' => 'ledger-summary',
            'parent' => 'doubleentry',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.ledger',
            'module' => $module,
            'permission' => 'report ledger'
        ]);
        $menu->add([
            'title' => __('Balance Sheet'),
            'icon' => '',
            'name' => 'balance-sheet',
            'parent' => 'doubleentry',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.balance.sheet',
            'module' => $module,
            'permission' => 'report balance sheet'
        ]);
        $menu->add([
            'title' => __('Profit & Loss'),
            'icon' => '',
            'name' => 'profit-loss',
            'parent' => 'doubleentry',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.profit.loss',
            'module' => $module,
            'permission' => 'report profit loss'
        ]);
        $menu->add([
            'title' => __('Trial Balance'),
            'icon' => '',
            'name' => 'trial-balance',
            'parent' => 'doubleentry',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.trial.balance',
            'module' => $module,
            'permission' => 'report trial balance'
        ]);
        $menu->add([
            'title' => __('Report'),
            'icon' => '',
            'name' => 'report',
            'parent' => 'doubleentry',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'report manage'
        ]);
        $menu->add([
            'title' => __('Sales'),
            'icon' => '',
            'name' => 'sales-report',
            'parent' => 'report',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.sales',
            'module' => $module,
            'permission' => 'report sales'
        ]);
        $menu->add([
            'title' => __('Receivables'),
            'icon' => '',
            'name' => 'receivables-report',
            'parent' => 'report',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.receivables',
            'module' => $module,
            'permission' => 'report receivables'
        ]);
        $menu->add([
            'title' => __('Payables'),
            'icon' => '',
            'name' => 'payables-report',
            'parent' => 'report',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.payables',
            'module' => $module,
            'permission' => 'report payables'
        ]);
    }
}
