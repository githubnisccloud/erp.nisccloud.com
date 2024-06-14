<?php

namespace Modules\LMS\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LMS\Entities\LmsUtility;
use Modules\LMS\Entities\Store;
use Modules\LMS\Entities\StoreThemeSetting;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\Entities\Certificate;
use Modules\LMS\Entities\Course;
use Illuminate\Support\Facades\DB;
use Modules\LMS\Entities\CourseOrder;

class LMSController extends Controller
{
    public function index()
    {
        if(\Auth::user()->isAbleTo('lms dashboard manage'))
        {
            $store = Store::where('workspace_id',getActiveWorkSpace())->where('created_by',creatorId())->first();
            $newproduct = Course::where('workspace_id', getActiveWorkSpace())->where('created_by',creatorId())->count();
            $products   = Course::where('workspace_id', getActiveWorkSpace())->where('created_by',creatorId())->limit(5)->get();
            $new_orders = CourseOrder::where('store_id', $store->id)->limit(5)->orderBy('id', 'DESC')->get();
            $course_orders     = CourseOrder::where('store_id', $store->id)->get();
            $chartData  = $this->getOrderChart(['duration' => 'week','store_id'=>$store->id]);

            $users = User::find(creatorId());

            if($store)
            {
                $app_url               = trim(env('APP_URL'), '/');
                $store['store_url'] = $app_url . '/store-lms/' . $store['slug'];
            }

            $total_sale  = 0;
            $total_order = 0;
            if(!empty($course_orders))
            {
                $pro_qty   = 0;
                $item_id   = [];
                $total_qty = [];
                foreach($course_orders as $course_order)
                {
                    $order_array = json_decode($course_order->course);
                    $pro_id      = [];
                    foreach($order_array as $key => $item)
                    {
                        if(!empty($item_id))
                        {
                            if(!in_array($item->id, $item_id))
                            {
                                $item_id[] = $item->id;
                            }
                        }
                        else
                        {
                            if(!in_array($item->id, $item_id))
                            {
                                $item_id[] = $item->id;
                            }
                        }
                    }
                    $total_sale += $course_order['price'];
                    $total_order++;
                }
            }

            return view('lms::dashboard.dashboard', compact('products', 'total_sale', 'store', 'course_orders', 'total_order', 'newproduct', 'item_id', 'total_qty', 'chartData', 'new_orders','users'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function getOrderChart($arrParam)
    {
        $store = Store::find($arrParam['store_id']);
        $arrDuration = [];
        if($arrParam['duration'])
        {
            if($arrParam['duration'] == 'week')
            {
                $previous_week = strtotime("-2 week +1 day");
                for($i = 0; $i < 14; $i++)
                {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach($arrDuration as $date => $label)
        {
            if(Auth::user()->type == 'Owner')
            {
                $data = CourseOrder::select(DB::raw('count(*) as total'))->where('store_id', $store->id)->whereDate('created_at', '=', $date)->first();
            }
            else
            {
                $data = CourseOrder::select(DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            }

            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }



    public function LmsStoreSetting(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'logo' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               'invoice_logo' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                           ]
        );


        if($request->enable_domain == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'domains' => 'required',
                               ]
            );
        }
        if($request->enable_domain == 'enable_subdomain')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'subdomain' => 'required',
                               ]
            );
        }

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $store = Store::where('workspace_id',getActiveWorkSpace())->first();
        if(!empty($request->logo))
        {
            if(!empty($store->logo))
            {
                delete_file($store->logo);
            }
            $filenameWithExt = $request->file('logo')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('logo')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $path = upload_file($request,'logo',$fileNameToStore,'lms_store_logo');
            if($path['flag'] == 1){
                $url = $path['url'];
            }
        }
        if(!empty($request->invoice_logo))
        {
            if(!empty($store->invoice_logo))
            {
                delete_file($store->invoice_logo);
            }
            $extension              = $request->file('invoice_logo')->getClientOriginalExtension();
            $fileNameToStoreInvoice = 'invoice_logo' . '_' . time() . '.' . $extension;

            $path = upload_file($request,'invoice_logo',$fileNameToStoreInvoice,'lms_store_logo');
            if($path['flag'] == 1){
                $url = $path['url'];
            }
        }

        if($request->enable_domain == 'enable_domain')
        {
            // Remove the http://, www., and slash(/) from the URL
            $input = $request->domains;
            // If URI is like, eg. www.way2tutorial.com/
            $input = trim($input, '/');
            // If not have http:// or https:// then prepend it
            if(!preg_match('#^http(s)?://#', $input))
            {
                $input = 'http://' . $input;
            }
            $urlParts = parse_url($input);
            // Remove www.
            $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
            // Output way2tutorial.com
        }
        if($request->enable_domain == 'enable_subdomain')
        {
            // Remove the http://, www., and slash(/) from the URL
            $input = env('APP_URL');

            // If URI is like, eg. www.way2tutorial.com/
            $input = trim($input, '/');
            // If not have http:// or https:// then prepend it
            if(!preg_match('#^http(s)?://#', $input))
            {
                $input = 'http://' . $input;
            }

            $urlParts = parse_url($input);

            // Remove www.
            $subdomain_name = preg_replace('/^www\./', '', $urlParts['host']);
            // Output way2tutorial.com
            $subdomain_name = $request->subdomain . '.' . $subdomain_name;
        }

        $store['name']  = $request->name;
        $store['email']  = $request->email;
        if($request->enable_domain == 'enable_domain')
        {
            $store['domains'] = $domain_name;
        }
        $store['enable_storelink'] = ($request->enable_domain == 'enable_storelink' || empty($request->enable_domain)) ? 'on' : 'off';
        $store['enable_domain']    = ($request->enable_domain == 'enable_domain') ? 'on' : 'off';
        $store['enable_subdomain'] = ($request->enable_domain == 'enable_subdomain') ? 'on' : 'off';
        if($request->enable_domain == 'enable_subdomain')
        {
            $store['subdomain'] = $subdomain_name;
        }
        $store['enable_rating']     = $request->enable_rating ?? 'off';
        $store['blog_enable']       = $request->blog_enable ?? 'off';
        $store['about']             = $request->about;
        $store['tagline']           = $request->tagline;
        $store['storejs']           = $request->storejs;
        $store['fbpixel_code']      = $request->fbpixel_code;
        $store['whatsapp']          = $request->whatsapp;
        $store['facebook']          = $request->facebook;
        $store['instagram']         = $request->instagram;
        $store['twitter']           = $request->twitter;
        $store['youtube']           = $request->youtube;
        $store['google_analytic']   = $request->google_analytic;
        $store['footer_note']       = $request->footer_note;
        $store['address']           = $request->address;
        $store['city']              = $request->city;
        $store['state']             = $request->state;
        $store['zipcode']           = $request->zipcode;
        $store['country']           = $request->country;
        $store['lang']              = $request->store_default_language;
        if(!empty($fileNameToStore))
        {
            $store['logo'] = $url;
        }
        if(!empty($fileNameToStoreInvoice))
        {
            $store['invoice_logo'] = $url;
        }
        $store->update();
        return redirect()->back()->with('success','Store setting save sucessfully.');
    }

    public function changeTheme(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'theme_color' => 'required',
                               'themefile' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $store                = Store::where('workspace_id',getActiveWorkSpace())->where('created_by',creatorId())->first();
        $store['store_theme'] = $request->theme_color;
        $store['theme_dir']   = $request->themefile;
        $store->save();

        return redirect()->back()->with('success', __('Theme Successfully Updated.'));
    }

    public function Editproducts($slug, $theme)
    {
        $store = Store::where('slug', $slug)->first();
        $getStoreThemeSetting = LmsUtility::getStoreThemeSetting($store->id, $theme);
        $getStoreThemeSetting1 = [];

        if( empty($getStoreThemeSetting) || empty(trim($getStoreThemeSetting['dashboard'])) ) {
            //json file
            $path = asset( 'Modules/LMS/Resources/assets/image/'. $store->theme_dir . "/" . $store->theme_dir . ".json" );
            $getStoreThemeSetting = json_decode(file_get_contents($path), true);
        } else {
            $getStoreThemeSetting = json_decode($getStoreThemeSetting['dashboard'], true);
            $getStoreThemeSetting1 = LmsUtility::getStoreThemeSetting($store->id, $theme);
        }
        return view('lms::company.edit_theme', compact('store', 'theme', 'getStoreThemeSetting','getStoreThemeSetting1'));
    }

    public function StoreEditProduct(Request $request, $slug, $theme)
    {
        $store = Store::where('slug', $slug)->first();
        $getStoreThemeSetting = LmsUtility::getStoreThemeSetting($store->id, $theme);
        if(!empty($getStoreThemeSetting['dashboard'])) {
            $getStoreThemeSetting = json_decode($getStoreThemeSetting['dashboard'], true);
        }

        $json = $request->array;
        foreach ($json as $key => $jsn) {

            foreach ($jsn['inner-list'] as $IN_key => $js)
            {

                if ($js['field_type'] == 'multi file upload')
                {
                    if (!empty($js['multi_image']))
                    {
                        foreach ($js['multi_image'] as $file)
                        {
                            $filenameWithExt = $file->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME) . '_brand';
                            $extension = $file->getClientOriginalExtension();
                            $fileNameToStore = $IN_key . '_' . rand(10, 100) . '_' . date('ymd') . time() . '.' . $extension;
                            $file_name[] = $fileNameToStore;

                            $path = multi_upload_file($file,'field_default_text',$fileNameToStore,$store->theme_dir.'/header');
                            if($path['flag'] == 1)
                            {
                                $url = $path['url'];
                            }else{
                                return redirect()->back()->with('error', __($path['msg']));
                            }
                            $new_path = $store->theme_dir . '/header/' . $fileNameToStore;
                            $json[$key]['inner-list'][$IN_key]['image_path'][] = $url;

                            $next_key_p_image = !empty($key_file) ? $key_file : 0;
                        }
                        if (!empty($jsn['prev_image']))
                        {
                            foreach ($jsn['prev_image'] as $p_key => $p_value) {
                                // $next_key_p_image = $next_key_p_image + 1;
                                $json[$key]['inner-list'][$IN_key]['image_path'][] = $p_value;
                            }
                        }
                    }else {
                        if(!empty($jsn['prev_image']))
                        {
                            foreach ($jsn['prev_image'] as $p_key => $p_value)
                            {
                                $json[$key]['inner-list'][$IN_key]['image_path'][] = $p_value;
                            }
                        }
                    }
                }
                if($js['field_type'] == 'photo upload')
                {
                    if ($jsn['array_type'] == 'multi-inner-list')
                    {

                        for ($i = 0; $i < $jsn['loop_number']; $i++)
                        {
                            if (!empty($json[$key][$js['field_slug']][$i]['image']) && gettype($json[$key][$js['field_slug']][$i]['image']) == 'object')
                            {
                                $file = $json[$key][$js['field_slug']][$i]['image'];

                                $filenameWithExt = $file->getClientOriginalName();
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME) ;
                                $extension = $file->getClientOriginalExtension();
                                $fileNameToStore = $i.'_'.rand(10,100).'_'.date('ymd') .time() .  '.'.$extension;
                                $file_name[] = $fileNameToStore;

                                $path = multi_upload_file($file,'field_default_text',$fileNameToStore,$store->theme_dir . '/header');
                                if($path['flag'] == 1){
                                    $url = $path['url'];
                                }else{
                                    return redirect()->back()->with('error', __($path['msg']));
                                }

                                if (!empty($file_name) && count($file_name) > 0) {
                                    $json[$key][$js['field_slug']][$i]['field_prev_text'] =  $url;
                                    $json[$key][$js['field_slug']][$i]['image'] = '';
                                }
                            } else{
                                $json[$key][$js['field_slug']][$i]['image'] = '';
                            }
                        }

                    } else {
                        if (gettype($js['field_default_text']) == 'object')
                        {
                            $file = $js['field_default_text'];
                            $filenameWithExt = $file->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME) ;
                            $extension = $file->getClientOriginalExtension();
                            $fileNameToStore = $filename  .date('ymd') .time() .  '.'.$extension;
                            $file_name[] = $fileNameToStore;

                            $requestImg['image'] = $file;
                            $myRequest = new Request();
                            $myRequest->request->add(['image' => $requestImg['image']]);
                            $myRequest->files->add(['image' => $requestImg['image']]);
                            $path = upload_file($myRequest,'image',$fileNameToStore,$store->theme_dir .'/header');
                            if (!empty($file_name) && count($file_name) > 0)
                            {
                                $post['Thumbnail Image'] =  $file_name;
                                foreach( $post['Thumbnail Image'] as $v)
                                {
                                    $headerImage = $store->theme_dir . '/header/' . $v;
                                }
                                $json[$key]['inner-list'][$IN_key]['field_default_text'] = $path['url'];
                            }
                        }

                    }
                }
            }
        }

        $json1 = json_encode($json);
        $store = Store::where('slug', $slug)->where('created_by', Auth::user()->id)->first();

        $where_array = [
            'name' => 'dashboard',
            'store_id' => $store->id,
            'theme_name' => $store->theme_dir,
        ];

        $update_create_array = [
            'name' => 'dashboard',
            'value' => $json1,
            'store_id' => $store->id,
            'theme_name' => $store->theme_dir,
            'created_by' => creatorId(),
        ];
        if(!empty($json1)) {
            StoreThemeSetting::updateOrCreate($where_array , $update_create_array);
        }

        return redirect()->back()->with('success', __('Successfully Saved!'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
    }

    public function saveCertificateSettings(Request $request)
    {
        $post = $request->all();
        unset($post['_token']);

        if(isset($post['certificate_template']) && (!isset($post['certificate_color']) || empty($post['certificate_color'])))
        {
            $post['certificate_color'] = "ffffff";
        }

        $store = Store::where('workspace_id',getActiveWorkSpace())->where('created_by',creatorId())->first();
        $store                        = Store::find($store->id);
        $store->certificate_template  = $request->certificate_template;
        $store->certificate_color     = $request->certificate_color;
        $store->certificate_gradiant  = $request->gradiant;
        $store->header_name           = $request->header_name;
        $store->save();

        return redirect()->back()->with('success', __('Certificate Setting updated successfully'));
    }

    public function previewCertificate($template, $color,$gradiants)
    {
        $objUser  = Auth::user();
        $settings = Store::saveCertificate();

        if(!empty($user)){
            $course_id = Course::where('id' , $user->courses_id)->first();
        } else {
            $course_id = 0;
        }

        $certificate  = new Certificate();

        $student                = new \stdClass();
        $student->name          = '<Name>';
        $student->course_name   = '<Course Name>';
        $student->course_time   = '<Course Time>';

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = LmsUtility::getFontColor($color);
        $gradiant   = $gradiants;

        return view('lms::company.settings.templates.' . $template, compact('certificate', 'preview', 'color', 'settings','student', 'font_color', 'gradiant','course_id'));
    }
}
