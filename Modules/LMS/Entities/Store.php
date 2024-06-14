<?php

namespace Modules\LMS\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Store extends Model
{
    use HasFactory;
    private static $fetchstore = null;

    protected $fillable = [
        'name',
        'email',
        'domains',
        'about',
        'tagline',
        'slug',
        'lang',
        'currency',
        'currency_code',
        'fbpixel_code',
        'whatsapp',
        'facebook',
        'instagram',
        'twitter',
        'footer_note',
        'address',
        'city',
        'state',
        'zipcode',
        'country',
        'logo',
        'is_stripe_enabled',
        'stripe_key',
        'stripe_secret',
        'is_paypal_enabled',
        'paypal_mode',
        'paypal_client_id',
        'paypal_secret_key',
        'invoice_template',
        'invoice_color',
        'invoice_footer_title',
        'invoice_footer_notes',
        'is_active',
        'theme_dir',
        'store_theme',
        'header_name',
        'certificate_template',
        'certificate_color',
        'certificate_gradiant',
        'workspace_id',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Modules\LMS\Database\factories\StoreFactory::new();
    }

    public static function create($data)
    {
        $obj          = new LmsUtility();
        $table        = with(new Store)->getTable();
        $data['slug'] = $obj->createSlug($table, $data['name']);
        $store        = static::query()->create($data);

        return $store;
    }
    public static function saveCertificate()
    {
        if(Auth::user())
        {
            $data = DB::table('stores')->where('workspace_id', '=', getActiveWorkSpace())->select('certificate_color','certificate_template','certificate_gradiant','header_name')->first();
            return $data;
        }
        else{
            $objUser  = Auth::guard('students')->user();
            $data = Store::where('id',$objUser->store_id)->select('certificate_color','certificate_template','certificate_gradiant','header_name')->first();
            return $data;
        }
    }

    public static function getStore($slug)
    {
        if(is_null(self::$fetchstore))
        {
            $store = Store::where('slug', $slug)->first();
            self::$fetchstore = $store;
        }
        return self::$fetchstore;
    }
}
