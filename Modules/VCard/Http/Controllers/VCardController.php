<?php

namespace Modules\VCard\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\VCard\Entities\Business;
use Modules\VCard\Entities\AppointmentDetails;
use Modules\VCard\Entities\ContactsDetails;
use Modules\VCard\Entities\Businessqr;

class VCardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::check()) {
            if (\Auth::user()->isAbleTo('vcard dashboard manage')) {
                $cards =Business::where('created_by', \creatorId())->where('workspace',getActiveWorkSpace())->get();

                $total_bussiness = Business::where('created_by', \creatorId())->where('workspace',getActiveWorkSpace())->count();
                $total_app = AppointmentDetails::where('created_by', \creatorId())->where('workspace',getActiveWorkSpace())->count();
                $total_contact = ContactsDetails::where('created_by', \creatorId())->where('workspace',getActiveWorkSpace())->count();

                $chartData = $this->getOrderChartData(['duration' => 'week']);

                $user = \Auth::user();
                $currentBusiness=Business::currentBusiness();
                $businessData = Business::where('id',$currentBusiness)->where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->first();
                $businesses=Business::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->pluck('title', 'id');
                $qr_detail='';
                if(!empty($businessData))
                {
                    $qr_detail = Businessqr::where('business_id', $businessData->id)->first();
                }
                return view('vcard::dashboard.dashboard', compact('total_bussiness', 'total_app', 'chartData', 'cards','total_contact','businessData','qr_detail','businesses'));

            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
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
        return redirect()->back();
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
        //
    }

    public function getOrderChartData($arrParam)
    {
        
        $arrDuration = [];
        if ($arrParam['duration']) {
            if ($arrParam['duration'] == 'week') {
                $previous_month = strtotime("-1 week");
                for ($i = 0; $i < 7; $i++) {
                    $arrDuration[date('Y-m-d', $previous_month)] = date('d-M', $previous_month);
                    $previous_month = strtotime(date('Y-m-d', $previous_month) . " +1 day");
                }
            }
        }
        $arrTask = [];
        $arrTask['label'] = [];
        $arrTask['data'] = [];
        $business = Business::where('created_by', \creatorId())->get();
        $array_app = [];
        foreach ($business as $b) {
            $d['data'] = [];
            $d['name'] = $b->title;
            foreach ($arrDuration as $date => $label) {
                $d['data'][] = \DB::table('appointment_details')->where('business_id', $b->id)->where('created_by', \creatorId())->where('workspace',getActiveWorkSpace())->whereDate('created_at', '=', $date)->count();
            }
            $array_app[] = $d;
        }
        $arrTask['data'] = $array_app;
        return $arrTask;
    }
}