<?php

namespace Modules\VCard\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VCard\Entities\Business;
use Modules\VCard\Entities\ContactsDetails;
use Modules\VCard\Entities\AppointmentDetails;
use Modules\VCard\Events\CreateContact;
use Modules\VCard\Events\DestroyContact;
use Modules\VCard\Events\UpdateContact;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id='')
    {
        
        if(\Auth::user()->isAbleTo('card contact manage'))
        {
            $businessData=Business::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('title', 'id');
            $currentBusiness=Business::currentBusiness();
            if($id==""){

                $contacts_details = ContactsDetails::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->where('business_id',$currentBusiness)->get();
                foreach ($contacts_details as $key => $value) {
                    $business_name = AppointmentDetails::getBusinessData($value->business_id);
                    $value->business_name = $business_name;
                }
            }
            else
            {
                $contacts_details = ContactsDetails::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->where('business_id',$id)->get();
                foreach ($contacts_details as $key => $value) {
                    $business_name = AppointmentDetails::getBusinessData($value->business_id);
                    $value->business_name = $business_name;
                }
            }
            return view('vcard::contacts.index',compact('contacts_details','businessData','id'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->back();
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
        $business = Business::where('id',$business_id)->first();
        
        $contact = ContactsDetails::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'created_by' => $business->created_by,
            'workspace' => $business->workspace,
        ]);
        event(new CreateContact($request,$contact)); 

        return redirect()->back()->with('success',__('Contact Created Successfully.'));

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
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if(\Auth::user()->isAbleTo('card contact delete'))
        {
            $contact = ContactsDetails::find($id);
            if($contact){
                event(new DestroyContact($contact));
                $contact->delete();
                return redirect()->back()->with('success', __('Contact successfully deleted.'));
            }
            return redirect()->back()->with('error', __('Contact not found.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function add_note($id)
    {
        $contact= ContactsDetails::where('id',$id)->first();
        return view('vcard::contacts.add_note',compact('contact'));
    }
    public function note_store($id,Request $request)
    {
        if(\Auth::user()->isAbleTo('card contact add note'))
        {
            $contacts = ContactsDetails::where('id',$id)->first();
            $contacts->status = $request->status;
            $contacts->note = $request->note;
            $contacts->save();
            event(new UpdateContact($request,$contacts));
            return redirect()->back()->with('success', __('Contact note added successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
