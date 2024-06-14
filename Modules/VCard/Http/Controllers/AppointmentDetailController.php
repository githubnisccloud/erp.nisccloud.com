<?php

namespace Modules\VCard\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\VCard\Entities\Business;
use Modules\VCard\Entities\AppointmentDetails;
use App\Models\User;
use App\Models\EmailTemplate;
use Exception;
use Modules\VCard\Events\CreateAppointment;
use Modules\VCard\Events\DestroyAppointment;
use Modules\VCard\Events\UpdateAppointment;



class AppointmentDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (\Auth::user()->isAbleTo('card appointment manage')) {
            $businessData = Business::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $currentBusiness = Business::currentBusiness();
            $appointment_details = AppointmentDetails::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('business_id', $currentBusiness)->orderBy('date', 'DESC')->get();
            foreach ($appointment_details as $key => $value) {
                $business_name = AppointmentDetails::getBusinessData($value->business_id);
                $value->business_name = $business_name;
            }
            return view('vcard::appointments.index', compact('appointment_details', 'businessData'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('vcard::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
        
        $business_id = $request->business_id;
        $business = Business::where('id', $business_id)->first();
        $appointment_details = AppointmentDetails::where('business_id', $business_id)->where('created_by', $business->created_by)->get();

        if ($appointment_details) {
            foreach ($appointment_details as $key => $value) {

                if ($value->date == $request->date && $value->time == $request->time) {
                    $data['msg'] = __("The appointment already booked.Please select another date or time.");
                    $data['flag'] = false;
                    return $data;
                }
            }
        }
        $user = User::where('id', $business->created_by)->first();
        $appointment = AppointmentDetails::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date' => $request->date,
            'time' => $request->time,
            'created_by' => $business->created_by,
            'workspace' => $business->workspace
        ]);

        // Google Calender

        event(new CreateAppointment($request, $appointment));
        $email = company_setting('company_email', $business->created_by, $business->workspace);

        if (!isset($email) || empty($email) || $email == null || $email == "") {
            $email = $user->email;
        }
        $settings = [];
        $settings['from_name'] = $appointment->name;
        $settings['from_email'] = $appointment->email;
        try {
            $appArr = [
                'appointment_name' => $request->name,
                'appointment_email' => $request->email,
                'appointment_phone' => $request->phone,
                'appointment_date' => $request->date,
                'appointment_time' => $request->time,
                'created_by' => $business->created_by,
            ];
            $resp = EmailTemplate::sendEmailTemplate('Appointment Created', [$appointment->id => $appointment->email], $appArr);
        } catch (\Exception $e) {
            $error = __('E-Mail has been not sent due to SMTP configuration');
        }
        return true;


    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->back();
        return view('vcard::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('vcard::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (\Auth::user()->isAbleTo('card appointment delete')) {
            $app = AppointmentDetails::find($id);
            if ($app) {
                event(new DestroyAppointment($app));
                $app->delete();
                return redirect()->back()->with('success', __('Appointment successfully deleted.'));
            }
            return redirect()->back()->with('error', __('Appointment not found.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function add_note($id)
    {
        $appointment = AppointmentDetails::where('id', $id)->first();
        return view('vcard::appointments.add_note', compact('appointment'));
    }

    public function note_store($id, Request $request)
    {

        if (\Auth::user()->isAbleTo('card appointment add note')) {
            $appointment = AppointmentDetails::where('id', $id)->first();
            $appointment->status = $request->status;
            $appointment->note = $request->note;
            $appointment->save();
            event(new UpdateAppointment($request, $appointment));
            return redirect()->back()->with('success', __('Appointment note added successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function appointmentCalender(Request $request, $id = null)
    {

        if (Auth::user()->isAbleTo('card appointment calendar')) {
            $businessData = Business::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $businessData->prepend('All', '');
            if ($id == null) {
                $appointents = AppointmentDetails::where('created_by', creatorId())->where('workspace', getActiveWorkSpace());
                if (!empty($request->start_date)) {
                    $appointents->where('date', '>=', $request->start_date);
                }
                if (!empty($request->end_date)) {
                    $appointents->where('date', '<=', $request->end_date);
                }
                if (!empty($request->business)) {
                    $appointents->where('business_id', '=', $request->business);
                }
                $appointents = $appointents->get();

            } else {

                $appointents = AppointmentDetails::where('business_id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            }
            $arrayJson = [];
            foreach ($appointents as $appointent) {
                $time = explode('-', $appointent->time);
                $stime = isset($time[0]) ? trim($time[0]) . ':00' : '00:00:00';
                $etime = isset($time[1]) ? trim($time[1]) . ':00' : '00:00:00';
                $start_date = date("Y-m-d", strtotime($appointent->date)) . ' ' . $stime;
                $end_date = date("Y-m-d", strtotime($appointent->date)) . ' ' . $etime;

                $arrayJson[] = [
                    "title" => '(' . $stime . ' - ' . $etime . ') ' . $appointent->name . '-' . $appointent->getBussinessName(),
                    "start" => $start_date,
                    "end" => $end_date,
                    "app_id" => $appointent->id,
                    "url" => route('appointment.details', $appointent->id),
                    "className" => 'event-info',
                    "allDay" => false,
                    "business_id" => $id,
                ];
            }
            $appointmentData = json_encode($arrayJson);
            return view('vcard::appointments.calender', compact('arrayJson', 'appointmentData', 'businessData'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getAppointmentDetails($id)
    {

        $ad = AppointmentDetails::find($id);
        return view('vcard::appointments.calender-modal', compact('ad'));
    }

}