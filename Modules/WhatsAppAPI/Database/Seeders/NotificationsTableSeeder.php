<?php

namespace Modules\WhatsAppAPI\Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $modules = [

            'General'       => ['Create User','New Invoice','Invoice Status Updated','New Proposal','Proposal Status Updated'] ,
            'Account'       => ['New Customer','New Bill','New Vendor','New Revenue','New Payment'] ,
            'Appointment'   => ['New Appointment', 'Appointment Status'] ,
            'CMMS'          => ['Work Order Request','New Supplier','New POs','Work Order Assigned' , 'New Part' , 'New Component' , 'New Location' , 'New Pms'] ,
            'Contract'      => ['New Contract'] ,
            'Fleet'         => ['New Vehicle', 'New Booking', 'New Maintenance', 'New Booking Payment', 'New Fuel'] ,
            'Hrm'           => ['New Monthly Payslip','New Award','New Event','Leave Approve/Reject','New Trip','New Announcement','New Holidays','New Company Policy'] ,
            'Lead'          => ['New Lead','Lead to Deal Conversion','New Deal','Lead Moved','Deal Moved'] ,
            'Pos'           => ['New Purchase'] ,
            'Recruitment'   => ['New Job Application','Interview Schedule','Convert To Employee'] ,
            'Retainer'      => ['Retainer create','New Retainer Payment'] ,
            'Rotas'         => ['RotaLeave Approve/Reject','New Rota','Rotas Time Change','New Availabilitys','Cancle Rotas','Days Off'] ,
            'Sales'         => ['New Quote','New Sales Order','New Sales Invoice','New Sales Invoice Payment','Meeting Assigned'] ,
            'SupportTicket' => ['New Ticket','New Ticket Reply'] ,
            'Taskly'        => ['New Project','New Task','New Bug'] ,
            'Training'      => ['New Trainer'] ,
            'VCard'         => ['New Appointment','New Contact','New Business','Business Status Updated'] ,
            'ZoomMeeting'   => ['New Zoom Meeting'] ,

        ];

        foreach($modules as $module_name => $actions)
        {
            foreach($actions as $action)
            {
                $ntfy = Notification::where('action',$action)->where('type','whatsappapi')->where('module' , $module_name)->count();
                if($ntfy == 0){
                    $new            = new Notification();
                    $new->action    = $action;
                    $new->status    = 'on';
                    $new->module    = $module_name;
                    $new->type      = 'whatsappapi';
                    $new->save();
                }
            }
        }
    }
}
