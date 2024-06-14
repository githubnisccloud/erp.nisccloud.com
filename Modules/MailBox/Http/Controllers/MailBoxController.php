<?php

namespace Modules\MailBox\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Webklex\IMAP\Message;
use Illuminate\Support\Facades\Validator;
use Webklex\IMAP\Facades\Client;
use Modules\MailBox\Emails\sendMail;
use Illuminate\Support\Facades\Mail;
class MailBoxController extends Controller
{

    public function setConfig()
    {
		
         config(['imap.accounts.default.host' => company_setting('mailbox_imap_server')]);
		 config(['imap.accounts.default.port' => company_setting('mailbox_imap_port')]);
		 config(['imap.accounts.default.encryption' => company_setting('mail_encryption')]);
		 config(['imap.accounts.default.username' => company_setting('mail_username')]);
		 config(['imap.accounts.default.password' => company_setting('mail_password')]);
       
    }
    public function setting(Request $request)
    {
		if(Auth::user()->isAbleTo('mailbox manage'))
        {
            if ($request->has('mailbox_is_on')) {
                $validator = Validator::make($request->all(),
                [
                    'mailbox_imap_server' => 'required|string',
					'mailbox_imap_port' => 'required',
                    'company_encryption_mode' => 'required'
                ]);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
            }
            $post = $request->all();
            unset($post['_token']);
            unset($post['_method']);
            if($request->has('mailbox_is_on')){
                foreach ($post as $key => $value) {
                    // Define the data to be updated or inserted
                    $data = [
                        'key' => $key,
                        'workspace' => getActiveWorkSpace(),
                        'created_by' => creatorId(),
                    ];

                    // Check if the record exists, and update or insert accordingly
                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            }
            else
            {
                  // Define the data to be updated or inserted
               $data = [
                    'key' => 'mailbox_is_on',
                    'workspace' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ];

                // Check if the record exists, and update or insert accordingly
                Setting::updateOrInsert($data, ['value' => 'off']);

            }

            // Settings Cache forget
            AdminSettingCacheForget();
            comapnySettingCacheForget();
			return redirect()->back()->with('success','EMail Box setting save sucessfully.');
		}
		else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($type="")
    {
		if(Auth::user()->isAbleTo('mailbox manage'))
        {
			$setConfig=$this->setConfig();
        	try {

				$client = Client::account('default');
				$client->connect();
				if($type=="sent")
				{
					$folder = $client->getFolder('Sent');
				}
				elseif($type=="trash")
				{
					$folder = $client->getFolderByName('Trash');
				}
				elseif($type=="drafts")
				{
					$folder = $client->getFolder('Drafts');
				}
				elseif($type=="spam")
				{
					$folder = $client->getFolder('spam');
				}
				elseif($type=="archive")
				{
					$folder = $client->getFolder('Archive');
				}
				else
				{
					$folder = $client->getFolder('INBOX');
				}
				if($type=='starred')
				{
					$messages=[];
					$emails = $folder->query()->all()->get();
					foreach ($emails as $email) {
							$flags = $email->getFlags();
							if ($flags->has('flagged')) {
								$messages[] = $email;
							}
						}
				}
				else
				{
					$messages = $folder->query()->all()->get();

				}
				
				return view('mailbox::mail.index',compact('messages'));
				
			} catch (\Throwable $th) {
				
				return redirect()->back()->with('error', __('Unable to connect with imap server'));
			}
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
		if(Auth::user()->isAbleTo('mailbox create'))
        {
        	return view('mailbox::mail.create');
		}
		else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
	{
		if(Auth::user()->isAbleTo('mailbox create'))
        {
			$attachments = [];
			if ($request->file('attachment')){
				foreach($request->file('attachment') as $key => $file)
				{
					
					$filenameWithExt = $request->file('attachment')[$key]->getClientOriginalName();
					$filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
					$extension       = $request->file('attachment')[$key]->getClientOriginalExtension();
					$fileNameToStore = $filename . '_' . time() . '.' . $extension;
					
					$uplaod = multi_upload_file($file, 'attachment', $fileNameToStore, 'mail_attachment');
					
					if ($uplaod['flag'] == 1) {
						$attachments[]= $uplaod['url'];
					} 
				}
			}
			
			$subject = $request->subject;
			$message = $request->content;
			$to = $request->to;
			$cc = $request->cc;
			$email = new sendMail($subject, $message);
			$email->to($to);
			if ($cc) {
				$email->cc($cc);
			}
			foreach ($attachments as $attachment) {
				$email->attach($attachment);
			}
			$user = Auth::user();
			$setconfing =SetConfigEmail($user->id);
			
			if ($setconfing ==  true) {
				try
				{
					Mail::send($email);
					return redirect()->route('mailbox.index', 'sent')->with('success', __('Mail sent successfully.'));
				}catch(\Exception $e)
				{
					
					return redirect()->route('mailbox.index', 'inbox')->with('error',$e);
				}
			}
			else {
				
				return redirect()->route('mailbox.index', 'inbox')->with('error',__('Something went wrong please try again'));
			}
		}
		else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
	}


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id,$type)
    {
		
		try{
			$setConfig=$this->setConfig();
			$client = Client::account('default');
			$client->connect();
			if($type=="sent")
			{
				$folder = $client->getFolder('Sent');
			}
			elseif($type=="trash")
			{
				$folder = $client->getFolderByName('Trash');
			}
			elseif($type=="drafts")
			{
				$folder = $client->getFolder('Drafts');
			}
			elseif($type=="spam")
			{
				$folder = $client->getFolderByName('spam');
			}
			elseif($type=="archive")
			{
				$folder = $client->getFolder('Archive');
			}
			else
			{
				$folder = $client->getFolder('INBOX');
			}

			$message = $folder->query()->getMessageByMsgn($id);
			
			if($message)
			{
			

				 return view('mailbox::mail.show')->with('message',$message);
			}
			else
			{
				return redirect()->route('mailbox.index', $type)->with('error',"Unable to fetch mail");
			}
		} 
		catch (\Throwable $th) 
		{
			
			return redirect()->back()->with('error', __('Unable to connect with imap server'));
		}
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('mailbox::edit');
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
    public function destroy($id,$type)
    {
        try{
			$setConfig=$this->setConfig();
			$client = Client::account('default');
			$client->connect();
			if($type=="sent")
			{
				$folder = $client->getFolder('Sent');
			}
			elseif($type=="trash")
			{
				$folder = $client->getFolderByName('Trash');
			}
			elseif($type=="drafts")
			{
				$folder = $client->getFolder('Drafts');
			}
			elseif($type=="spam")
			{
				$folder = $client->getFolderByName('spam');
			}
			elseif($type=="archive")
			{
				$folder = $client->getFolder('Archive');
			}
			else
			{
				$folder = $client->getFolder('INBOX');
			}

			$message = $folder->query()->getMessageByMsgn($id);
			
			if($message)
			{
				$message->delete();
				return redirect()->route('mailbox.index', $type)->with('success',"Mail deleted successfully.");
			}
			else
			{
				return redirect()->route('mailbox.index', $type)->with('error',"Mail isAbleTo not delete.");
			}
		} 
		catch (\Throwable $th) 
		{
			
			return redirect()->back()->with('error', __('Unable to connect with imap server'));
		}
    }
	public function action(Request $request)
    {
		
		if(Auth::user()->isAbleTo('mailbox manage'))
        {
		
			try {
					$setConfig=$this->setConfig();
					$client = Client::account('default');
					$client->connect(); 
					if($request->folder=="sent")
					{
						$folder = $client->getFolder('Sent');
					}
					elseif($request->folder=="trash")
					{
						$folder = $client->getFolderByName('Trash');
					}
					elseif($request->folder=="drafts")
					{
						$folder = $client->getFolder('Drafts');
					}
					elseif($request->folder=="spam")
					{
						$folder = $client->getFolder('spam');
					}
					elseif($request->folder=="archive")
					{
						$folder = $client->getFolder('Archive');
					}
					else
					{
						$folder = $client->getFolder('INBOX');
					}
					$result="";
					foreach($request->id as $id)
					{
						$message = $folder->query()->getMessageByMsgn($id);
						if($request->action=='starred')
						{
							$message->setFlag('Flagged');
							$flag=$message->getFlags();
							$result=["status" =>1,'msg'=>__('Mail starred successfully.')];
						}
						if($request->action=='unstarred')
						{
							$message->removeFlag('Flagged');
							$flag=$message->getFlags();
							$flag=$message->getFlags();
							$result=["status" =>0,'msg'=>__('Mail starred successfully.')];
						}
						if($request->action=='seen')
						{
							$message->setFlag('Seen');
							$flag=$message->getFlags();
							$result=["status" =>1,'msg'=>__('Mail marked as seen successfully.')];
						}
						if($request->action=='unseen')
						{
							$message->removeFlag('Seen');
							$flag=$message->getFlags();
							$flag=$message->getFlags();
							$result=["status" =>0,'msg'=>__('Mail marked as unseen successfully.')];
						}
						if($request->action=='move')
						{
							$moveToFolder = $client->getFolder($request->moveToFolder);
							$message->move($moveToFolder->path);
							$result=["status" =>1,'msg'=>__('Mail move successfully')];
						}
					}
					return $result;
				} catch (Exception $e) {
                    
				return redirect()->back()->with('error', __('Unable to connect with imap server'));
    			}
		}else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
