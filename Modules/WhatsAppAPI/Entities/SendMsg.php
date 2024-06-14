<?php

namespace Modules\WhatsAppAPI\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GuzzleHttp\Client;


class SendMsg extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\WhatsAppAPI\Database\factories\SendMsgFactory::new();
    }

    public static function SendMsgs($mobile_no , $msg = '')
    {

        $whatsappapi_notification_is        = !empty(company_setting('whatsappapi_notification_is')) ? company_setting('whatsappapi_notification_is') : '';
        $whatsapp_phone_number_id             = !empty(company_setting('whatsapp_phone_number_id')) ? company_setting('whatsapp_phone_number_id'): '';
        $whatsapp_access_token    = !empty(company_setting('whatsapp_access_token')) ? company_setting('whatsapp_access_token'):'';
        

        if (($whatsappapi_notification_is == 'on') && (!empty($whatsapp_phone_number_id)) && (!empty($whatsapp_access_token)))
        {
            try
            {
                
                $url = 'https://graph.facebook.com/v17.0/'.$whatsapp_phone_number_id.'/messages';

                $data = array(
                    'messaging_product' => 'whatsapp',
                    // 'recipient_type' => 'individual',
                    'to' => $mobile_no,
                    'type' => 'text',
                    'text' => array(
                        'preview_url' => false,
                        'body' => $msg
                    )
                );

                $headers = array(
                    'Authorization: Bearer '.$whatsapp_access_token,
                    'Content-Type: application/json'
                );

                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);
                $responseData = json_decode($response);

                curl_close($ch);

            }
            catch(\Exception $e)
            {
                return $e;
            }
        }
        else
        {
            return false;
        }
    }
}
