<?php

namespace Modules\VCard\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Http;
use JeroenDesloovere\VCard\VCard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\VCard\Entities\AppointmentDetails;
use Modules\VCard\Entities\Business;
use Modules\VCard\Entities\Businessfield;
use Modules\VCard\Entities\business_hours;
use Modules\VCard\Entities\ContactsDetails;
use Modules\VCard\Entities\Gallery;
use Illuminate\Validation\Rules;
use Modules\VCard\Entities\ContactInfo;
use Modules\VCard\Entities\Appoinment;
use Modules\VCard\Entities\Service;
use Modules\VCard\Entities\Testimonial;
use Modules\VCard\Entities\Social;
use Modules\VCard\Entities\PixelFields;
use Modules\VCard\Entities\Businessqr;
use Modules\VCard\Events\BusinessStatus;
use Modules\VCard\Events\CreateBusiness;
use Modules\VCard\Events\DestroyBusiness;
use Modules\VCard\Events\UpdateBusiness;
use Modules\VCard\Events\EditTheme;
use Session;
use Modules\VCard\Entities\CardProduct;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (\Auth::user()->isAbleTo('business manage')) {
            $business = Business::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('id', 'DESC')->get();
            $no = 0;
            foreach ($business as $key => $value) {
                $value->no = $no;
                $no++;
            }
            return view('vcard::business.index', compact('business'));
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
        if (\Auth::user()->isAbleTo('business create')) {
            $themeOne = Business::themeOne();
            return view('vcard::business.create', compact('themeOne'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('business create')) {
            $user = \Auth::user();
            $count = Business::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->count();

            $validator = \Validator::make(
                $request->all(),
                [
                    'business_title' => 'required',
                    'theme' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $slug = Business::createSlug('businesses', $request->business_title);

            $card_theme = [];
            $card_theme['theme'] = $request->theme;
            $card_theme['order'] = Business::getDefaultThemeOrder($request->theme);
            $business = Business::create([
                'title' => $request->business_title,
                'slug' => $slug,
                'branding_text' => 'Copyright © ' . env('APP_NAME') . ' ' . date("Y"),
                'card_theme' => json_encode($card_theme),
                'theme_color' => !empty($request->theme_color) ? $request->theme_color : 'color1-' . $request->theme,
                'workspace' => getActiveWorkSpace(),
                'created_by' => creatorId(),
            ]);
            $business->enable_businesslink = 'on';
            $business->is_branding_enabled = 'on';
            if ($count <= 0) {
                $business->current_business = 1;
            } else {
                $business->current_business = 0;
            }
            $business->save();

            event(new CreateBusiness($request, $business));

            return redirect()->back()->with('success', __('Business Created Successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
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


    public function editTheme($id, Request $request)
    {
        if (\Auth::user()->isAbleTo('business theme settings')) {
            $count = Business::where('id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->count();
            if ($count == 0) {
                return redirect()->route('business.index')->with('error', __('This card number is not yours.'));
            }
            $validator = \Validator::make(
                $request->all(),
                [
                    'theme_color' => 'required',
                    'themefile' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $card_order = [];
            $card_order['theme'] = $request->themefile;
            $card_order['order'] = Business::getDefaultThemeOrder($request->themefile);
            $businesss = Business::where('id', $id)->first();
            $businesss['theme_color'] = $request->theme_color;
            $businesss['card_theme'] = json_encode($card_order);
            $businesss->save();
            event(new EditTheme($request, $businesss));
            $tab = 1;
            return redirect()->back()->with('success', __('Theme Successfully Updated.'))->with('tab', $tab);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('business manage')) {

            $business = Business::where('id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();

            if ($business->status != 'locked') {
                $themeOne = Business::themeOne();
                $businessfields = Businessfield::getFields();

                $appoinment = appoinment::cardAppointmentData($business->id);
                $appoinment_hours = [];

                if (!empty($appoinment->content)) {
                    $appoinment_hours = json_decode($appoinment->content);
                }

                $contactinfo = ContactInfo::cardContactData($business->id);
                $contactinfo_content = [];
                if (!empty($contactinfo->content)) {
                    $contactinfo_content = json_decode($contactinfo->content);
                }
                $services = Service::cardServiceData($business->id);
                $services_content = [];
                if (!empty($services->content)) {
                    $services_content = json_decode($services->content);
                }
                $testimonials = Testimonial::cardTestimonialData($business->id);

                $testimonials_content = [];
                if (!empty($testimonials->content)) {
                    $testimonials_content = json_decode($testimonials->content);
                }
                $sociallinks = Social::cardSocialData($business->id);
                $social_content = [];
                if (!empty($sociallinks->content)) {
                    $social_content = json_decode($sociallinks->content);
                }

                $days = business_hours::$days;
                $businesshours = business_hours::cardBusinessHour($business->id);
                $business_hours = [];
                if (!empty($businesshours->content)) {
                    $business_hours = json_decode($businesshours->content);
                }

                $customhtml = $business;
                $custom_html = [];
                if (!empty($customhtml->custom_html_text)) {
                    $custom_html = json_decode($customhtml->custom_html_text);
                }
                $branding = [];

                if (!empty($business->branding_text)) {
                    $branding = $business->branding_text;
                }


                $app_url = trim(env('APP_URL'), '/');
                $business_url = $app_url . '/cards/' . $business['slug'];


                $serverName = str_replace(
                    [
                        'http://',
                        'https://',
                    ],
                    '',
                    env('APP_URL')
                );
                $serverIp = gethostbyname($serverName);

                if ($serverIp == $_SERVER['SERVER_ADDR']) {
                    $serverIp;
                } else {
                    $serverIp = request()->server('SERVER_ADDR');
                }

                if (!empty($business->enable_subdomain) && $business->enable_subdomain == 'on') {
                    // Remove the http://, www., and slash(/) from the URL
                    $input = env('APP_URL');

                    // If URI is like, eg. www.way2tutorial.com/
                    $input = trim($input, '/');
                    // If not have http:// or https:// then prepend it
                    if (!preg_match('#^http(s)?://#', $input)) {
                        $input = 'http://' . $input;
                    }

                    $urlParts = parse_url($input);

                    // Remove www.
                    $subdomain_name = preg_replace('/^www\./', '', $urlParts['host']);
                    // Output way2tutorial.com
                } else {
                    $subdomain_name = str_replace(
                        [
                            'http://',
                            'https://',
                        ],
                        '',
                        env('APP_URL')
                    );
                }

                $gallaryoption = Gallery::$gallaryOption;
                $gallery = Gallery::cardGalleryData($business->id);
                $gallery_contents = [];
                if (!empty($gallery->content)) {
                    $gallery_contents = json_decode($gallery->content);
                }

                $PixelFields = PixelFields::where('business_id', $business->id)->get();
                $pixelScript = [];
                foreach ($PixelFields as $pixel) {

                    if (!$pixel->disabled) {
                        $pixelScript[] = Business::pixelSourceCode($pixel['platform'], $pixel['pixel_id']);
                    }
                }

                // Cookie Data
                $cookieDetail = [];
                $filename = '';

                $filename = $business->slug . '.csv';
                $cookieDetail = json_decode($business->gdpr_text);

                $qr_code = Business::$qr_type;
                $qr_detail = Businessqr::where('business_id', $business->id)->first();

                try {
                    $pwa_data = \File::get('uploads/theme_app/business_' . $business->id . '/manifest.json');
                    $pwa_data = json_decode($pwa_data);


                } catch (\Throwable $th) {
                    $pwa_data = '';
                }

                $subdomain_Ip = '';
                $subdomainPointing = '';
                $domainip = '';
                $domainPointing = '';

                $mp4_msg = null;
                $storage_settings = getAdminAllSetting();
                $mimes = !empty($storage_settings['local_storage_validation']) ? $storage_settings['local_storage_validation'] : 'jpeg,jpg,png,svg,zip,txt,gif,docx';

                if (strpos($mimes, 'mp4') !== false) {
                    $mp4_msg = '';
                } else {
                    $mp4_msg = 'You can`t upload mp4 video because superadmin has not allowed it in storage settings.';
                }
                $products = CardProduct::cardProductData($business->id);
                $products_content = [];
                if (!empty($products->content)) {
                    $products_content = json_decode($products->content);
                }
                $tab = 1;
                $currencyData = Business::getCurrency();
                return view('vcard::business.edit', compact('themeOne', 'businessfields', 'appoinment_hours', 'contactinfo', 'contactinfo_content', 'appoinment', 'services_content', 'services', 'testimonials_content', 'testimonials', 'social_content', 'sociallinks', 'businesshours', 'business_hours', 'business', 'customhtml', 'branding', 'days', 'id', 'business_url', 'serverIp', 'subdomain_name', 'pwa_data', 'gallery_contents', 'gallery', 'PixelFields', 'pixelScript', 'cookieDetail', 'filename', 'qr_code', 'qr_detail', 'subdomain_Ip', 'subdomainPointing', 'domainip', 'domainPointing', 'gallaryoption', 'mp4_msg', 'products', 'products_content', 'currencyData'))->with('tab', $tab);
            } else {
                return redirect()->back()->with('error', __('Business is locked'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
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

        if (\Auth::user()->isAbleTo('business edit')) {
            $user = \Auth::user();
            $business = Business::where('id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();

            if (!is_null($business)) {
                $count = Business::where('id', $business->id)->where('created_by', creatorId())->count();
                if ($count == 0) {
                    return redirect()->route('business.index')->with('error', __('This card number is not yours.'));
                }
                if (is_null($business->banner) || is_null($business->logo)) {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'banner' => 'required',
                            'logo' => 'required',
                        ]
                    );
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();

                        return redirect()->back()->with('error', $messages->first());
                    }
                }

                $count = Business::where('slug', $request->slug)->count();
                if (!is_null($business)) {
                    if ($count == 0) {
                        $business->slug = $request->slug;
                    } elseif ($count == 1) {
                        if ($business->slug != $request->slug) {
                            return redirect()->route('business.index')->with('error', __('Slug is already used.........!'));
                        }

                    }
                }
                $business->title = $request->title;
                $business->sub_title = $request->sub_title;
                $business->description = $request->description;
                $business->designation = $request->designation;

                if ($request->hasFile('logo')) {
                    $filenameWithExt = $request->file('logo')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('logo')->getClientOriginalExtension();
                    $fileName = 'logo_' . $filename . time() . rand() . '.' . $extension;


                    $uploadcard_logo = upload_file($request, 'logo', $fileName, 'card_logo');
                    if ($uploadcard_logo['flag'] == 1) {
                        $url = $uploadcard_logo['url'];
                    } else {
                        return redirect()->back()->with('error', $uploadcard_logo['msg']);
                    }
                    $business->logo = $url;

                }

                if ($request->hasFile('banner')) {
                    $filenameWithExt = $request->file('banner')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('banner')->getClientOriginalExtension();
                    $fileName = 'banner_' . $filename . time() . rand() . '.' . $extension;


                    $uploadcard_banner = upload_file($request, 'banner', $fileName, 'card_banner');
                    if ($uploadcard_banner['flag'] == 1) {
                        $url = $uploadcard_banner['url'];
                    } else {
                        return redirect()->back()->with('error', $uploadcard_banner['msg']);
                    }
                    $business->banner = $url;

                }
                //Contact Info
                if ($request->is_contacts_enabled == "on") {
                    $contacts_content = json_encode($request->contact);
                    $contactsinfo = ContactInfo::where('business_id', $business->id)->first();
                    if (!is_null($contactsinfo)) {
                        if ($contacts_content != 'null') {
                            $contactsinfo->content = $contacts_content;
                            $contactsinfo->is_enabled = '1';
                            $contactsinfo->created_by = creatorId();
                            $contactsinfo->save();

                        } else {
                            $contactsinfo->content = $contacts_content;
                            $contactsinfo->is_enabled = '1';
                            $contactsinfo->created_by = creatorId();
                            $contactsinfo->save();
                        }

                    } else {

                        ContactInfo::create([
                            'business_id' => $business->id,
                            'content' => $contacts_content,
                            'is_enabled' => '1',
                            'created_by' => creatorId()
                        ]);
                    }
                } else {
                    $contactsinfo = ContactInfo::where('business_id', $business->id)->first();
                    if (!is_null($contactsinfo)) {
                        $contactsinfo->is_enabled = '0';
                        $contactsinfo->created_by = creatorId();
                        $contactsinfo->save();
                    } else {
                        ContactInfo::create([
                            'business_id' => $business->id,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }

                //Business Hours
                if ($request->is_business_hours_enabled == "on") {
                    $requestAll = $request->all();
                    $days = business_hours::$days;
                    $business_hours = [];
                    foreach ($days as $k => $day) {
                        $time['days'] = isset($requestAll['days_' . $k]) ? 'on' : 'off';
                        $time['start_time'] = $requestAll['start-' . $k];
                        $time['end_time'] = $requestAll['end-' . $k];
                        $business_hours[$k] = $time;
                    }
                    $business_hours = json_encode($business_hours);
                    $businessHours = business_hours::where('business_id', $business->id)->first();
                    if (!is_null($businessHours)) {
                        $businessHours->content = $business_hours;
                        $businessHours->is_enabled = '1';
                        $businessHours->created_by = creatorId();
                        $businessHours->save();
                    } else {
                        business_hours::create([
                            'business_id' => $business->id,
                            'content' => $business_hours,
                            'is_enabled' => '1',
                            'created_by' => creatorId()
                        ]);
                    }
                } else {
                    $businessHours = business_hours::where('business_id', $business->id)->first();
                    if (!is_null($businessHours)) {
                        $businessHours->is_enabled = '0';
                        $businessHours->created_by = creatorId();
                        $businessHours->save();
                    } else {
                        business_hours::create([
                            'business_id' => $business->id,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }

                //appointment
                if ($request->is_appoinment_enabled == "on") {
                    $app_hours = $request->hours;
                    $appointment_count = 1;
                    $appoinment_hours = [];
                    $hours = [];
                    if (!empty($app_hours)) {
                        foreach ($app_hours as $business_hours_key => $business_hours_val) {
                            $hours['id'] = $appointment_count;
                            $hours['start'] = $business_hours_val['start'];
                            $hours['end'] = $business_hours_val['end'];
                            $appoinment_hours[$business_hours_key] = $hours;
                            $appointment_count++;
                        }
                        $appoinment_hours = json_encode($appoinment_hours);
                        $appoinment = Appoinment::where('business_id', $business->id)->first();
                        if (!is_null($appoinment)) {
                            $appoinment->content = $appoinment_hours;
                            $appoinment->is_enabled = '1';
                            $appoinment->created_by = creatorId();
                            $appoinment->save();
                        } else {
                            Appoinment::create([
                                'business_id' => $business->id,
                                'content' => $appoinment_hours,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }

                    } else {
                        $appoinment_hours = json_encode($appoinment_hours);
                        $appoinment = Appoinment::where('business_id', $business->id)->first();
                        if (!is_null($appoinment)) {
                            $appoinment->content = $appoinment_hours;
                            $appoinment->is_enabled = '1';
                            $appoinment->created_by = creatorId();
                            $appoinment->save();
                        } else {
                            Appoinment::create([
                                'business_id' => $business->id,
                                'content' => $appoinment_hours,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }

                    }
                } else {
                    $appoinment = Appoinment::where('business_id', $business->id)->first();
                    if (!is_null($appoinment)) {
                        $appoinment->is_enabled = '0';
                        $appoinment->created_by = creatorId();
                        $appoinment->save();
                    } else {
                        Appoinment::create([
                            'business_id' => $business->id,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }
                //service
                if ($request->is_services_enabled == "on") {

                    $servicedetails = $request->services;
                    $service_count = 1;
                    $service_details = [];
                    $details = [];
                    $requestImg = [];
                    if (!empty($servicedetails)) {

                        foreach ($servicedetails as $service_key => $service_val) {
                            $images = $request->file('services');
                            $details['id'] = $service_count;
                            $details['title'] = $service_val['title'];
                            $details['description'] = $service_val['description'];
                            $details['purchase_link'] = $service_val['purchase_link'];
                            $details['link_title'] = $service_val['link_title'];

                            if (isset($images[$service_key])) {
                                $filenameWithExt = $images[$service_key]['image']->getClientOriginalName();
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                $extension = $images[$service_key]['image']->getClientOriginalExtension();
                                $fileNameToStore = 'img' . $filename . '_' . time() . '.' . $extension;


                                $requestImg['image'] = $images[$service_key]['image'];
                                $myRequest = new Request();
                                $myRequest->request->add(['image' => $requestImg['image']]);
                                $myRequest->files->add(['image' => $requestImg['image']]);
                                $upload_service_image = upload_file($myRequest, 'image', $fileNameToStore, 'service_images');


                                if ($upload_service_image['flag'] == 1) {
                                    $url = $upload_service_image['url'];
                                } else {
                                    return redirect()->back()->with('error', $upload_service_image['msg']);
                                }
                                $details['image'] = $url;


                            } else {

                                if (isset($service_val['get_image']) && !is_null($service_val['get_image'])) {
                                    $details['image'] = $service_val['get_image'];
                                } else {
                                    $details['image'] = "";
                                }
                            }

                            $service_details[$service_key] = $details;
                            $service_count++;

                        }
                        $service_details = json_encode($service_details);

                        $services_data = service::where('business_id', $business->id)->first();
                        if (!is_null($services_data)) {

                            if ($service_details != 'null') {
                                $services_data->content = $service_details;
                                $services_data->is_enabled = '1';
                                $services_data->created_by = creatorId();
                                $services_data->save();
                            } else {
                                $services_data->is_enabled = '1';
                                $services_data->created_by = creatorId();
                                $services_data->save();
                            }
                        } else {
                            service::create([
                                'business_id' => $business->id,
                                'content' => $service_details,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }
                    } else {
                        $service_details = json_encode($service_details);
                        $services_data = Service::where('business_id', $business->id)->first();
                        if (!is_null($services_data)) {

                            if ($service_details != 'null') {
                                $services_data->content = $service_details;
                                $services_data->is_enabled = '1';
                                $services_data->created_by = creatorId();
                                $services_data->save();
                            } else {
                                $services_data->is_enabled = '1';
                                $services_data->created_by = creatorId();
                                $services_data->save();
                            }
                        } else {
                            Service::create([
                                'business_id' => $business->id,
                                'content' => $service_details,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }
                    }
                } else {
                    $services_data = Service::where('business_id', $business->id)->first();
                    if (!is_null($services_data)) {
                        $services_data->is_enabled = '0';
                        $services_data->created_by = creatorId();
                        $services_data->save();
                    } else {
                        Service::create([
                            'business_id' => $business->id,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }

                // Testimonial
                if ($request->is_testimonials_enabled == "on") {

                    $testimonialsdetails = $request->testimonials;
                    $testimonials_count = 1;
                    $testimonials_details = [];
                    $testi_details = [];
                    $requestImg = [];

                    if (!empty($testimonialsdetails)) {
                        foreach ($testimonialsdetails as $testimonials_key => $testimonials_val) {
                            $testimonials_images = $request->file('testimonials');
                            $testi_details['id'] = $testimonials_count;
                            if (isset($testimonials_val['rating'])) {
                                $testi_details['rating'] = $testimonials_val['rating'];
                            } else {
                                $testi_details['rating'] = "0";
                            }
                            if (isset($testimonials_val['description']) && $testimonials_val['description'] != 'null') {
                                $testi_details['description'] = $testimonials_val['description'];
                            } else {
                                $testi_details['description'] = '';
                            }

                            if (isset($testimonials_images[$testimonials_key])) {
                                $filenameWithExt = $testimonials_images[$testimonials_key]['image']->getClientOriginalName();
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                $extension = $testimonials_images[$testimonials_key]['image']->getClientOriginalExtension();
                                $fileNameToStore = 'img' . $filename . '_' . time() . '.' . $extension;


                                $requestImg['image'] = $testimonials_images[$testimonials_key]['image'];
                                $testimonialRequest = new Request();
                                $testimonialRequest->request->add(['image' => $requestImg['image']]);
                                $testimonialRequest->files->add(['image' => $requestImg['image']]);

                                $upload_testimonial_image = upload_file($testimonialRequest, 'image', $fileNameToStore, 'testimonials_images');

                                if ($upload_testimonial_image['flag'] == 1) {
                                    $url1 = $upload_testimonial_image['url'];
                                } else {
                                    return redirect()->back()->with('error', $upload_testimonial_image['msg']);
                                }
                                $testi_details['image'] = $url1;


                            } else {
                                if (isset($testimonials_val['get_image']) && !is_null($testimonials_val['get_image'])) {
                                    $testi_details['image'] = $testimonials_val['get_image'];
                                } else {
                                    $testi_details['image'] = '';

                                }
                            }
                            $testimonials_details[$testimonials_key] = $testi_details;
                            $testimonials_count++;
                        }
                        $testimonials_details = json_encode($testimonials_details);


                        $testimonials_data = testimonial::where('business_id', $business->id)->first();
                        if (!is_null($testimonials_data)) {
                            if ($testimonials_details != 'null') {
                                $testimonials_data->content = $testimonials_details;
                                $testimonials_data->is_enabled = '1';
                                $testimonials_data->created_by = creatorId();
                                $testimonials_data->save();
                            } else {
                                $testimonials_data->is_enabled = '1';
                                $testimonials_data->created_by = creatorId();
                                $testimonials_data->save();
                            }
                        } else {
                            testimonial::create([
                                'business_id' => $business->id,
                                'content' => $testimonials_details,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }
                    } else {
                        $testimonials_details = json_encode($testimonials_details);

                        $testimonials_data = testimonial::where('business_id', $business->id)->first();
                        if (!is_null($testimonials_data)) {
                            if ($testimonials_details != 'null') {
                                $testimonials_data->content = $testimonials_details;
                                $testimonials_data->is_enabled = '1';
                                $testimonials_data->created_by = creatorId();
                                $testimonials_data->save();
                            } else {
                                $testimonials_data->is_enabled = '1';
                                $testimonials_data->created_by = creatorId();
                                $testimonials_data->save();
                            }
                        } else {
                            testimonial::create([
                                'business_id' => $business->id,
                                'content' => $testimonials_details,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }
                    }


                } else {
                    $testimonials_data = testimonial::where('business_id', $business->id)->first();
                    if (!is_null($testimonials_data)) {
                        $testimonials_data->is_enabled = '0';
                        $testimonials_data->created_by = creatorId();
                        $testimonials_data->save();
                    } else {
                        testimonial::create([
                            'business_id' => $business->id,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }

                //Product
                if ($request->is_product_enabled == "on") {
                    $productdetails = $request->product;
                    $product_count = 1;
                    $product_details = [];
                    $prdetails = [];
                    $requestProductImg = [];
                    if (!empty($productdetails)) {
                        foreach ($productdetails as $product_key => $product_val) {

                            $pr_images = $request->file('product');
                            $prdetails['id'] = $product_count;
                            $prdetails['title'] = $product_val['title'];
                            $prdetails['description'] = $product_val['description'];
                            $prdetails['price'] = $product_val['price'];
                            $prdetails['purchase_link'] = $product_val['purchase_link'];
                            $prdetails['link_title'] = $product_val['link_title'];
                            $prdetails['currency'] = $product_val['currency'];

                            if (isset($pr_images[$product_key])) {
                                $filenameWithExt = $pr_images[$product_key]['image']->getClientOriginalName();
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                $extension = $pr_images[$product_key]['image']->getClientOriginalExtension();
                                $fileNameToStore = 'img' . $filename . '_' . time() . '.' . $extension;


                                $requestProductImg['image'] = $pr_images[$product_key]['image'];
                                $myRequestProduct = new Request();
                                $myRequestProduct->request->add(['image' => $requestProductImg['image']]);
                                $myRequestProduct->files->add(['image' => $requestProductImg['image']]);
                                $upload_product_image = upload_file($myRequestProduct, 'image', $fileNameToStore, 'card_product_image');


                                if ($upload_product_image['flag'] == 1) {
                                    $url = $upload_product_image['url'];
                                } else {
                                    return redirect()->back()->with('error', $upload_product_image['msg']);
                                }
                                $prdetails['image'] = $url;
                            } else {
                                if (isset($product_val['get_image']) && !is_null($product_val['get_image'])) {
                                    $prdetails['image'] = $product_val['get_image'];
                                } else {
                                    $prdetails['image'] = "";
                                }
                            }
                            $product_details[$product_key] = $prdetails;
                            $product_count++;

                        }
                        $product_details = json_encode($product_details);

                        $product_data = CardProduct::where('business_id', $business->id)->first();
                        if (!is_null($product_data)) {

                            if ($product_details != 'null') {
                                $product_data->content = $product_details;
                                $product_data->is_enabled = '1';
                                $product_data->created_by = creatorId();
                                $product_data->save();
                            } else {
                                $product_data->is_enabled = '1';
                                $product_data->created_by = creatorId();
                                $product_data->save();
                            }
                        } else {
                            CardProduct::create([
                                'business_id' => $business->id,
                                'content' => $product_details,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }
                    } else {
                        $product_details = json_encode($product_details);
                        $product_data = CardProduct::where('business_id', $business->id)->first();
                        if (!is_null($product_data)) {

                            if ($product_details != 'null') {
                                $product_data->content = $product_details;
                                $product_data->is_enabled = '1';
                                $product_data->created_by = creatorId();
                                $product_data->save();
                            } else {
                                $product_data->is_enabled = '1';
                                $product_data->created_by = creatorId();
                                $product_data->save();
                            }
                        } else {
                            CardProduct::create([
                                'business_id' => $business->id,
                                'content' => $product_details,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }
                    }
                } else {
                    $product_data = CardProduct::where('business_id', $business->id)->first();
                    if (!is_null($product_data)) {
                        $product_data->is_enabled = '0';
                        $product_data->created_by = creatorId();
                        $product_data->save();
                    } else {
                        CardProduct::create([
                            'business_id' => $business->id,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }

                //social
                if ($request->is_socials_enabled == "on") {

                    $sociallinks_content = json_encode($request->socials);
                    $sociallinks = Social::where('business_id', $business->id)->first();

                    if (!is_null($sociallinks)) {

                        if ($sociallinks_content != 'null') {
                            $sociallinks->content = $sociallinks_content;
                            $sociallinks->is_enabled = '1';
                            $sociallinks->created_by = creatorId();
                            $sociallinks->save();
                        } else {

                            $sociallinks->content = $sociallinks_content;
                            $sociallinks->is_enabled = '1';
                            $sociallinks->created_by = creatorId();
                            $sociallinks->save();
                        }

                    } else {
                        if ($sociallinks_content != 'null') {
                            Social::create([
                                'business_id' => $business->id,
                                'content' => $sociallinks_content,
                                'is_enabled' => '1',
                                'created_by' => creatorId()
                            ]);
                        }
                    }
                } else {
                    $sociallinks = Social::where('business_id', $business->id)->first();
                    if (!is_null($sociallinks)) {
                        $sociallinks->is_enabled = '0';
                        $sociallinks->created_by = creatorId();
                        $sociallinks->save();
                    } else {
                        Social::create([
                            'business_id' => $business->id,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }

                //Custom Html
                if ($request->is_custom_html_enabled == "on") {
                    $custom_html = str_replace(array("\r\n"), "", $request->custom_html_text);
                    $custom_html_text = Business::where('id', $business->id)->first();
                    if (!is_null($custom_html_text)) {

                        $custom_html_text->custom_html_text = $custom_html;
                        $custom_html_text->is_custom_html_enabled = '1';
                        $custom_html_text->created_by = creatorId();
                        $custom_html_text->save();

                    } else {
                        Business::create([
                            'id' => $business->id,
                            'customhtml' => $custom_html,
                            'is_enabled' => '1',
                            'created_by' => creatorId()
                        ]);
                    }
                } else {
                    $custom_html = str_replace(array("\r\n"), "", $request->custom_html_text);
                    $custom_html_text = Business::where('id', $business->id)->first();
                    if (!is_null($custom_html_text)) {

                        $custom_html_text->custom_html_text = $custom_html;
                        $custom_html_text->is_custom_html_enabled = '0';
                        $custom_html_text->created_by = creatorId();
                        $custom_html_text->save();

                    } else {
                        Business::create([
                            'id' => $business->id,
                            'customhtml' => $custom_html,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }

                if ($request->is_gallery_enabled == "on") {
                    $gallery_data = explode(",", $request->galary_data); //pass when data is not empty

                    $galleryNewImage = '';
                    $details = [];
                    $gallery_details = [];
                    $gallery_content = [];
                    $image_data = '';

                    $galleryinfo = Gallery::where('business_id', $business->id)->first();
                    if (!empty($galleryinfo->content)) {
                        $gallery_content = (array) json_decode($galleryinfo->content);
                        foreach ($gallery_content as $key => $data) {
                            $image_data = $data->value;
                        }
                    }

                    if ($request->hasFile('upload_video')) {
                        $filenameWithExt = $request->file('upload_video')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $video = $request->file('upload_video');
                        $extension = $request->file('upload_video')->getClientOriginalExtension();
                        $fileName = 'video_' . $filename . time() . rand() . '.' . $extension;
                        $upload_gallery_video = upload_file($request, 'upload_video', $fileName, 'gallery');

                        if ($upload_gallery_video['flag'] == 1) {
                            $url = $upload_gallery_video['url'];
                        } else {
                            return redirect()->back()->with('error', $upload_gallery_video['msg']);
                        }
                        $galleryNewImage = $url;
                    }



                    if ($request->hasFile('upload_image')) {
                        $filenameWithExt = $request->file('upload_image')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $images = $request->file('upload_image');
                        $extension = $request->file('upload_image')->getClientOriginalExtension();
                        $fileName = 'image_' . $filename . time() . rand() . '.' . $extension;


                        $upload_gallery_image = upload_file($request, 'upload_image', $fileName, 'gallery');

                        if ($upload_gallery_image['flag'] == 1) {
                            $url = $upload_gallery_image['url'];
                        } else {
                            return redirect()->back()->with('error', $upload_gallery_image['msg']);
                        }
                        $galleryNewImage = $url;
                    }
                    if ($request->galleryoption == 'custom_image_link') {
                        $galleryNewImage = $request->custom_image_link;
                    }

                    if ($request->galleryoption == 'custom_video_link') {
                        $galleryNewImage = $request->custom_video_link;
                    }

                    if ($request->galleryoption != null && $galleryNewImage != '') {

                        $details['id'] = $request->gallery_count;
                        $details['type'] = $request->galleryoption;
                        $details['value'] = $galleryNewImage;
                        $gallery_details = (object) $details;
                        $gallery_content[] = $gallery_details;
                    }


                    $gallery_contents = [];
                    foreach ($gallery_content as $key => $value) {
                        $gallery_contents[] = [
                            'id' => $key,
                            'type' => $value->type,
                            'value' => $value->value,
                        ];
                    }


                    if (!is_null($galleryinfo)) {
                        if ($gallery_details != 'null') {
                            $galleryinfo->content = json_encode($gallery_contents);
                            $galleryinfo->is_enabled = '1';
                            $galleryinfo->created_by = creatorId();
                            $galleryinfo->save();

                        } else {
                            $galleryinfo->content = $gallery_details;
                            $galleryinfo->is_enabled = '1';
                            $galleryinfo->created_by = creatorId();
                            $galleryinfo->save();
                        }

                    } else {

                        Gallery::create([
                            'business_id' => $business->id,
                            'content' => json_encode($gallery_contents),
                            'is_enabled' => '1',
                            'created_by' => creatorId()
                        ]);
                    }


                } else {

                    $gallery_info = Gallery::where('business_id', $business->id)->first();
                    if (!is_null($gallery_info)) {
                        $gallery_info->is_enabled = '0';
                        $gallery_info->created_by = creatorId();
                        $gallery_info->save();
                    } else {
                        Gallery::create([
                            'business_id' => $business->id,
                            'is_enabled' => '0',
                            'created_by' => creatorId()
                        ]);
                    }
                }

                $business->workspace = getActiveWorkSpace();
                $business->created_by = creatorId();
                $business->save();
                event(new UpdateBusiness($request, $business));
                $tab = 2;
                return redirect()->back()->with('success', __('Business Information Add Successfully'))->with('tab', $tab);

            } else {

                return redirect()->back()->with('Error', __('Business does not exist'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
        if (\Auth::user()->isAbleTo('business delete')) {
            $count = Business::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->count();

            if ($count == 0) {
                return redirect()->route('business.index')->with('error', __('This card number is not yours.'));
            }
            if ($count > 1) {
                $business = Business::where('id', $id)->where('workspace', getActiveWorkSpace())->first();
                if ($business->current_business == 1) {
                    $currentBusiness = Business::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
                    $currentBusiness->current_business = 1;
                    $currentBusiness->save();
                }
                event(new DestroyBusiness($business));
                $business->delete();
                $businessqr = Businessqr::where('business_id', $id)->delete();
                AppointmentDetails::where('business_id', $id)->delete();
                ContactsDetails::where('business_id', $id)->delete();


                return redirect()->back()->with('success', __('Business Information Deleted Successfully'));
            } else {
                return redirect()->back()->with('error', __('You have only one business'));
            }

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function domainsetting($id, Request $request)
    {

        if (\Auth::user()->isAbleTo('business custom settings')) {

            $count = Business::where('id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->count();
            if ($count == 0) {
                return redirect()->route('business.index')->with('error', __('This card number is not yours.'));
            }
            $business = Business::where('id', $id)->first();


            if ($request->enable_domain == 'enable_domain') {
                // Remove the http://, www., and slash(/) from the URL
                $input = $request->domains;
                // If URI is like, eg. www.way2tutorial.com/
                $input = trim($input, '/');
                // If not have http:// or https:// then prepend it
                if (!preg_match('#^http(s)?://#', $input)) {
                    $input = 'http://' . $input;
                }

                $urlParts = parse_url($input);
                // Remove www.
                $domain_name = preg_replace('/^www\./', '', $urlParts['host'] ?? null);

                // Output way2tutorial.com
            }
            if ($request->enable_domain == 'enable_subdomain') {
                // Remove the http://, www., and slash(/) from the URL
                $input = env('APP_URL');

                // If URI is like, eg. www.way2tutorial.com/
                $input = trim($input, '/');
                // If not have http:// or https:// then prepend it
                if (!preg_match('#^http(s)?://#', $input)) {
                    $input = 'http://' . $input;
                }

                $urlParts = parse_url($input);

                // Remove www.
                $subdomain_name = preg_replace('/^www\./', '', $urlParts['host']);
                // Output way2tutorial.com
                $subdomain_name = $request->subdomain . '.' . $subdomain_name;
            }

            if ($request->enable_domain == 'enable_domain') {
                $business['domains'] = $domain_name;
            }

            $business['enable_businesslink'] = ($request->enable_domain == 'enable_businesslink' || empty($request->enable_domain)) ? 'on' : 'off';
            $business['enable_domain'] = ($request->enable_domain == 'enable_domain') ? 'on' : 'off';
            $business['enable_subdomain'] = ($request->enable_domain == 'enable_subdomain') ? 'on' : 'off';

            if ($request->enable_domain == 'enable_subdomain') {
                $business['subdomain'] = $subdomain_name;
            }
            $business->save();


            //CustomJs And CustomCSS

            if ($request->has('customjs') || $request->has('customcss')) {

                $business = Business::find($id);
                $business->customjs = $request->customjs;
                $business->customcss = $request->customcss;
                $business->save();
            }

            //Google_Fonts
            if ($request->has('google_fonts')) {

                $business = Business::find($id);
                $business->google_fonts = $request->google_fonts;
                $business->save();

            }

            //Password
            if ($request->password && $request->is_password_enabled) {

                $request->validate([
                    'password' => Rules\Password::defaults(),
                ]);
                $business = Business::find($id);
                $business->password = $request->password;
                $business->enable_password = $request->is_password_enabled;
                $business->save();

            }

            //Branding
            if ($request->branding_text) {

                $business = Business::find($id);
                $business->is_branding_enabled = $request->branding;
                $business->branding_text = $request->branding_text;
                $business->save();

            }
            $tab = 3;
            return redirect()->back()->with('success', __('Custom Detail Successfully Updated.'))->with('tab', $tab);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function blocksetting($id, Request $request)
    {

        if (\Auth::user()->isAbleTo('business block settings')) {
            $count = Business::where('id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->count();
            if ($count == 0) {
                return redirect()->route('business.index')->with('error', __('This card number is not yours.'));
            }
            $business = Business::where('id', $id)->where('workspace', getActiveWorkSpace())->first();
            $card_order = [];
            $order = [];
            $card_order['theme'] = $request->theme_name;
            $req_order = explode(",", $request->order);
            foreach ($req_order as $key => $value) {
                $od = $key + 1;
                $order[$value] = $od;
            }
            $card_order['order'] = $order;
            $business->card_theme = $card_order;
            $business->save();

            $contact_data = ContactInfo::where('business_id', $id)->first();
            if ($contact_data != NULL) {
                $contact_data['is_enabled'] = $request->is_contact_info_enabled == 'on' ? '1' : '0';
                $contact_data->save();
            } else {
                ContactInfo::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_contact_info_enabled == 'on' ? '1' : '0',
                    'created_by' => creatorId()
                ]);
            }

            $bussiness_hour_data = business_hours::where('business_id', $id)->first();
            if ($bussiness_hour_data != NULL) {
                $bussiness_hour_data['is_enabled'] = $request->is_bussiness_hour_enabled == 'on' ? '1' : '0';
                $bussiness_hour_data->save();
            } else {
                business_hours::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_bussiness_hour_enabled == 'on' ? '1' : '0',
                    'created_by' => creatorId()
                ]);
            }

            $appointment_data = appoinment::where('business_id', $id)->first();
            if ($appointment_data != NULL) {
                $appointment_data['is_enabled'] = $request->is_appointment_enabled == 'on' ? '1' : '0';
                $appointment_data->save();
            } else {
                appoinment::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_appointment_enabled == 'on' ? '1' : '0',
                    'created_by' => creatorId()
                ]);
            }

            $service_data = service::where('business_id', $id)->first();
            if ($service_data != NULL) {
                $service_data['is_enabled'] = $request->is_service_enabled == 'on' ? '1' : '0';
                $service_data->save();
            } else {
                service::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_service_enabled == 'on' ? '1' : '0',
                    'created_by' => creatorId()
                ]);
            }

            $testimonials_data = testimonial::where('business_id', $id)->first();
            if ($testimonials_data != NULL) {
                $testimonials_data['is_enabled'] = $request->is_testimonials_enabled == 'on' ? '1' : '0';
                $testimonials_data->save();
            } else {
                testimonial::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_testimonials_enabled == 'on' ? '1' : '0',
                    'created_by' => creatorId()
                ]);
            }

            $social_data = social::where('business_id', $id)->first();
            if ($social_data != NULL) {
                $social_data['is_enabled'] = $request->is_social_enabled == 'on' ? '1' : '0';
                $social_data->save();
            } else {
                social::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_social_enabled == 'on' ? '1' : '0',
                    'created_by' => creatorId()
                ]);
            }

            //Gallery
            $gallery_data = Gallery::where('business_id', $id)->first();
            if ($gallery_data != NULL) {
                $gallery_data['is_enabled'] = $request->is_gallery_enabled == 'on' ? '1' : '0';
                $gallery_data->save();
            } else {
                Gallery::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_gallery_enabled == 'on' ? '1' : '0',
                    'created_by' => creatorId()
                ]);
            }

            $custom_html = Business::where('id', $id)->first();
            if ($custom_html != NULL) {
                $custom_html['is_custom_html_enabled'] = $request->is_custom_html_enabled == 'on' ? '1' : '0';
                $custom_html->save();
            } else {
                Business::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_custom_html_enabled == 'on' ? '1' : '0',
                    'created_by' => creatorId()
                ]);
            }

            $branding = Business::where('id', $id)->first();
            $product_data = CardProduct::where('business_id', $id)->first();
            if ($product_data != NULL) {
                $product_data['is_enabled'] = $request->is_product_enabled == 'on' ? '1' : '0';
                $product_data->save();
            } else {
                CardProduct::create([
                    'business_id' => $id,
                    'is_enabled' => $request->is_product_enabled == 'on' ? '1' : '0',
                    'created_by' => \Auth::user()->creatorId()
                ]);
            }
            $tab = 4;
            return redirect()->back()->with('success', __('Block Order Successfully Updated.'))->with('tab', $tab);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //Pixels
    public function pixel_create($business_id)
    {
        $pixals_platforms = Business::pixel_plateforms();
        return view('vcard::pixelfield.create', compact('pixals_platforms', 'business_id'));
    }

    public function pixel_store(Request $request)
    {

        if (\Auth::user()->isAbleTo('business pixel settings')) {
            $request->validate([
                'platform' => 'required',
                'pixel_id' => 'required'
            ]);
            $pixel_fields = new PixelFields();
            $pixel_fields->platform = $request->platform;
            $pixel_fields->pixel_id = $request->pixel_id;
            $pixel_fields->business_id = $request->business_id;
            $pixel_fields->created_by = creatorId();
            $pixel_fields->save();
            $tab = 5;
            return redirect()->back()->with('success', __('Pixelfield Created Successfully'))->with('tab', $tab);
        } else {
            return redirect()->back()->with('error', __('permission Denied'));
        }
    }
    public function pixeldestroy($id)
    {
        if (\Auth::user()->isAbleTo('business pixel settings')) {
            $user = \Auth::user();
            $PixelFields = PixelFields::where('id', $id)->first();

            $PixelFields->delete();
            $tab = 5;
            return redirect()->back()->with('success', __('Pixelfield Successfully Deleted'))->with('tab', $tab);
            ;
        } else {
            return redirect()->back()->with('error', __('permission Denied'));
        }
    }

    public function saveCookiesetting(Request $request, $id)
    {
        $count = Business::where('id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->count();
        if ($count == 0) {
            return redirect()->route('business.index')->with('error', __('This card number is not yours.'));
        }
        $business = Business::where('id', $id)->where('workspace', getActiveWorkSpace())->first();

        if ($request->enable_cookie && $request->enable_cookie == 'on') {
            $cookieData['cookie_logging'] = $request->cookie_logging;
            $cookieData['cookie_title'] = $request->cookie_title;
            $cookieData['cookie_description'] = $request->cookie_description;
            $cookieData['strictly_cookie_title'] = $request->strictly_cookie_title;
            $cookieData['strictly_cookie_description'] = $request->strictly_cookie_description;
            $cookieData['more_information_description'] = $request->more_information_description;
            $cookieData['contactus_url'] = $request->contactus_url;

            $business = Business::find($id);
            $business->is_gdpr_enabled = $request->enable_cookie;
            $business->gdpr_text = json_encode($cookieData);
            $business->save();
        } else {
            $business->is_gdpr_enabled = $request->enable_cookie;
            $business->save();
        }
        $tab = 7;
        return redirect()->back()->with('success', __('Cookie-Setting Successfully Updated.'))->with('tab', $tab);

    }
    public function cardCookieConsent(Request $request)
    {
        $data = Business::where('slug', '=', $request->slug)->first();

        $filename = '';
        $filename = $data->slug . '.csv';
        $settings = json_decode($data->gdpr_text);

        if ($request['cookie']) {
            if ($data->is_gdpr_enabled == "on" && $settings->cookie_logging == "on") {

                $allowed_levels = ['necessary', 'analytics', 'targeting'];
                $levels = array_filter($request['cookie'], function ($level) use ($allowed_levels) {
                    return in_array($level, $allowed_levels);
                });

                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                // Generate new CSV line
                $browser_name = $whichbrowser->browser->name ?? null;
                $os_name = $whichbrowser->os->name ?? null;
                $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
                $device_type = GetDeviceType($_SERVER['HTTP_USER_AGENT']);

               // $ip = $_SERVER['REMOTE_ADDR'];
                  $ip = '49.36.83.154';
                $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));


                $date = (new \DateTime())->format('Y-m-d');
                $time = (new \DateTime())->format('H:i:s') . ' UTC';


                $new_line = implode(',', [
                    $ip,
                    $date,
                    $time,
                    json_encode($request['cookie']),
                    $device_type,
                    $browser_language,
                    $browser_name,
                    $os_name,
                    isset($query) ? $query['country'] : '',
                    isset($query) ? $query['region'] : '',
                    isset($query) ? $query['regionName'] : '',
                    isset($query) ? $query['city'] : '',
                    isset($query) ? $query['zip'] : '',
                    isset($query) ? $query['lat'] : '',
                    isset($query) ? $query['lon'] : ''
                ]);
                if (!check_file('/uploads/sample/' . $filename . '.csv')) {
                    $first_line = 'IP,Date,Time,Accepted-cookies,Device type,Browser anguage,Browser name,OS Name,Country,Region,RegionName,City,Zipcode,Lat,Lon';
                    file_put_contents(base_path() . '/uploads/sample/' . $filename, $first_line . PHP_EOL, FILE_APPEND | LOCK_EX);
                }
                file_put_contents(base_path() . '/uploads/sample/' . $filename, $new_line . PHP_EOL, FILE_APPEND | LOCK_EX);


                return response()->json('success');
            }
            return response()->json('error');
        }
        return redirect()->back();
    }

    //Custom Qr code
    public function saveCustomQrsetting(Request $request, $id)
    {

        $business = Businessqr::where('business_id', $id)->first();
        if ($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = $filename . '_' . time() . '.' . $extension;

            $upload_qr = upload_file($request, 'image', $fileName, 'qrcode');
            if ($upload_qr['flag'] == 1) {
                $url = $upload_qr['url'];
            } else {
                return redirect()->back()->with('error', $upload_qr['msg']);
            }
            $qrImage = $url;
        }
        if (empty($business)) {
            $business = new Businessqr();

        }

        if (!isset($fileName)) {
            $qrImage = isset($business->image) ? $business->image : null;
        }

        $business->business_id = $id;
        $business->foreground_color = isset($request->foreground_color) ? $request->foreground_color : '#000000';
        $business->background_color = isset($request->background_color) ? $request->background_color : '#ffffff';
        $business->radius = isset($request->radius) ? $request->radius : 26;
        $business->qr_type = isset($request->qr_type) ? $request->qr_type : 0;
        $business->qr_text = isset($request->qr_text) ? $request->qr_text : "Vcard";
        $business->qr_text_color = isset($request->qr_text_color) ? $request->qr_text_color : '#f50a0a';
        $business->size = isset($request->size) ? $request->size : 9;
        $business->image = isset($qrImage) ? $qrImage : null;
        $business->save();
        $tab = 8;
        return redirect()->back()->with('success', 'QrCode generated successfully')->with('tab', $tab);

    }
    //PWA
    public function savePWA(Request $request, $id)
    {

        if (\Auth::user()->isAbleTo('business PWA settings')) {
            $business_id = $id;
            $business = Business::find($id);
            $business['enable_pwa_business'] = $request->pwa_business ?? 'off';

            if ($request->pwa_business == 'on') {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'pwa_app_title' => 'required|max:100',
                        'pwa_app_name' => 'required|max:50',
                        'pwa_app_background_color' => 'required|max:15',
                        'pwa_app_theme_color' => 'required|max:15',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $company_favicon = get_module_img('VCard');
                $lang = \Auth::user()->lang;

                if ($business['enable_businesslink'] == 'on') {
                    $start_url = url('/cards/'  . $business['slug']);
                } else if ($business['enable_domain'] == 'on') {
                    $start_url = 'https://' . $business['domains'] . '/';
                } else {
                    $start_url = 'https://' . $business['subdomain'] . '/';
                }


                $mainfest = '{
                                "lang": "' . $lang . '",
                                "name": "' . $request->pwa_app_title . '",
                                "short_name": "' . $request->pwa_app_name . '",
                                "start_url": "' . $start_url . '",
                                "display": "standalone",
                                "background_color": "' . $request->pwa_app_background_color . '",
                                "theme_color": "' . $request->pwa_app_theme_color . '",
                                "orientation": "portrait",
                                "categories": [
                                    "shopping"
                                ],
                                "icons": [
                                    {
                                        "src": "' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '",
                                        "sizes": "80x80",
                                        "type": "image/png",
                                        "purpose": "any"
                                    },
                                    {
                                        "src": "' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '",
                                        "sizes": "128x128",
                                        "type": "image/png",
                                        "purpose": "any"
                                    },
                                    {
                                        "src": "' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '",
                                        "sizes": "144x144",
                                        "type": "image/png",
                                        "purpose": "any"
                                    },
                                    {
                                        "src": "' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '",
                                        "sizes": "152x152",
                                        "type": "image/png",
                                        "purpose": "any"
                                    },
                                    {
                                        "src": "' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '",
                                        "sizes": "192x192",
                                        "type": "image/png",
                                        "purpose": "any"
                                    },
                                    {
                                        "src": "' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '",
                                        "sizes": "256x256",
                                        "type": "image/png",
                                        "purpose": "any"
                                    },
                                    {
                                        "src": "' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '",
                                        "sizes": "512x512",
                                        "type": "image/png",
                                        "purpose": "any"
                                    },
                                    {
                                        "src": "' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '",
                                        "sizes": "1024x1024",
                                        "type": "image/png",
                                        "purpose": "any"
                                    }
                                ]                                
                            }';


                if (!file_exists('uploads/theme_app/business_' . $business_id)) {
                    mkdir('uploads/theme_app/business_' . $business_id, 0777, true);
                }
                if (!file_exists('uploads/theme_app/business_' . $business_id . '/manifest.json')) {
                    fopen('uploads/theme_app/business_' . $business_id . "/manifest.json", "w");
                }
                \File::put('uploads/theme_app/business_' . $business_id . '/manifest.json', $mainfest);
            }

            $business->save();
            $tab = 6;
            return redirect()->back()->with('success', __('PWA Successfully Updated.'))->with('tab', $tab);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveseo(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('business SEO settings')) {
            $business = Business::find($id);
            $business->meta_keyword = $request->meta_keyword;
            $business->meta_description = $request->meta_description;
            $business->google_analytic = $request->google_analytic;
            $business->fbpixel_code = $request->fbpixel_code;

            if ($request->hasFile('meta_image')) {
                $fileName1 = isset($business->image) ? $business->image : null;
                $filenameWithExt = $request->file('meta_image')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('meta_image')->getClientOriginalExtension();
                $fileName = 'meta_image_' . $filename . '_' . time() . '.' . $extension;



                $upload_meta_img = upload_file($request, 'meta_image', $fileName, 'card_meta_image');
                if ($upload_meta_img['flag'] == 1) {
                    $url = $upload_meta_img['url'];
                } else {
                    return redirect()->back()->with('error', $upload_meta_img['msg']);
                }
                $business->meta_image = $url;
            }
            $business->save();
            $tab = 5;
            return redirect()->back()->with('success', __('SEO Successfully Updated.'))->with('tab', $tab);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function getcard($slug)
    {
        $business = Business::getBusinessBySlug($slug);

        if (!is_null($business)) {
            if ($business->status != 'locked') {
                \App::setLocale($business->getLanguage());
                $is_slug = "true";

                $businessfields = Businessfield::getFields();
                $businesshours = business_hours::where('business_id', $business->id)->first();
                $appoinment = Appoinment::where('business_id', $business->id)->first();
                $appoinment_hours = [];
                if (!empty($appoinment->content)) {
                    $appoinment_hours = json_decode($appoinment->content);
                }

                $services = Service::where('business_id', $business->id)->first();
                $services_content = [];
                if (!empty($services->content)) {
                    $services_content = json_decode($services->content);
                }

                $testimonials = Testimonial::where('business_id', $business->id)->first();
                $testimonials_content = [];
                if (!empty($testimonials->content)) {
                    $testimonials_content = json_decode($testimonials->content);
                }

                $contactinfo = ContactInfo::where('business_id', $business->id)->first();
                $contactinfo_content = [];
                if (!empty($contactinfo->content)) {
                    $contactinfo_content = json_decode($contactinfo->content);
                }

                $sociallinks = Social::where('business_id', $business->id)->first();
                $social_content = [];
                if (!empty($sociallinks->content)) {
                    $social_content = json_decode($sociallinks->content);
                }

                //Gallery
                $gallery = Gallery::where('business_id', $business->id)->first();
                $gallery_contents = [];
                if (!empty($gallery->content)) {
                    $gallery_contents = json_decode($gallery->content);
                }


                $customhtml = Business::where('id', $business->id)->first();
                $days = business_hours::$days;
                $business_hours = '';
                if (!empty($businesshours->content)) {
                    $business_hours = json_decode($businesshours->content);
                }
                if (json_decode($business->card_theme) == NULL) {
                    $card_order = [];
                    $card_order['theme'] = $business->card_theme;
                    $card_order['order'] = Business::getDefaultThemeOrder($business->card_theme);
                    $business->card_theme = json_encode($card_order);
                    $business->save();
                }
                $card_theme = json_decode($business->card_theme);

                $pixels = PixelFields::where('business_id', $business->id)->get();
                $pixelScript = [];
                foreach ($pixels as $pixel) {

                    if (!$pixel->disabled) {
                        $pixelScript[] = Business::pixelSourceCode($pixel['platform'], $pixel['pixel_id']);
                    }
                }
                $products = CardProduct::where('business_id', $business->id)->first();
                $products_content = [];
                if (!empty($products->content)) {
                    $products_content = json_decode($products->content);
                }

                $qr_detail = Businessqr::where('business_id', $business->id)->first();
                return view('vcard::card.' . $card_theme->theme . '.index', compact('businessfields', 'contactinfo', 'contactinfo_content', 'appoinment_hours', 'appoinment', 'services_content', 'services', 'testimonials_content', 'testimonials', 'social_content', 'sociallinks', 'customhtml', 'businesshours', 'business_hours', 'business', 'days', 'is_slug', 'gallery', 'gallery_contents', 'pixelScript', 'qr_detail', 'products', 'products_content'));
            } else {
                return abort('403', 'The Link You Followed Has Inactive');
            }

        } else {
            return abort('403', 'The Link You Followed Has Expired');
        }
    }

    public function downloadqr(Request $request)
    {
        $qrData = $request->qrData;
        $business = Business::where('slug', $qrData)->where('workspace', getActiveWorkSpace())->first();
        $qr_detail = Businessqr::where('business_id', $business->id)->first();

        $view = view('vcard::business.businessQrCode', compact('qrData', 'business', 'qr_detail'))->render();

        $data['success'] = true;
        $data['data'] = $view;
        return $data;

    }

    public function destroyGallery(Request $request)
    {
        $id = $request->business_id;
        $data_id = $request->id;

        $gallery = Gallery::where('business_id', $id)->first();
        $gallery_details = json_decode($gallery->content);

        $gallery_detailss = [];
        foreach ($gallery_details as $key => $data) {
            if ($data->id != $data_id) {
                $gallery_detailss[] = $data;
            }
        }

        $gallery_content = json_encode($gallery_detailss);
        $gallery->content = $gallery_content;
        $gallery->save();
        Session::put(['tab' => 2]);
        return true;
    }


    public function cardpdf($slug)
    {
        $business = Business::where('slug', $slug)->where('workspace', getActiveWorkSpace())->first();


        if (!is_null($business)) {
            \App::setLocale($business->getLanguage());
            $is_slug = "true";
            $is_pdf = "true";
            $businessfields = Businessfield::getFields();
            $businesshours = business_hours::where('business_id', $business->id)->first();
            $appoinment = Appoinment::where('business_id', $business->id)->first();
            $appoinment_hours = [];
            if (!empty($appoinment->content)) {
                $appoinment_hours = json_decode($appoinment->content);
            }

            $services = Service::where('business_id', $business->id)->first();
            $services_content = [];
            if (!empty($services->content)) {
                $services_content = json_decode($services->content);
            }

            $testimonials = Testimonial::where('business_id', $business->id)->first();
            $testimonials_content = [];
            if (!empty($testimonials->content)) {
                $testimonials_content = json_decode($testimonials->content);
            }

            $contactinfo = ContactInfo::where('business_id', $business->id)->first();
            $contactinfo_content = [];
            if (!empty($contactinfo->content)) {
                $contactinfo_content = json_decode($contactinfo->content);
            }

            $sociallinks = Social::where('business_id', $business->id)->first();
            $social_content = [];
            if (!empty($sociallinks->content)) {
                $social_content = json_decode($sociallinks->content);
            }

            //Gallery
            $gallery = Gallery::where('business_id', $business->id)->first();
            $gallery_contents = [];
            if (!empty($gallery->content)) {
                $gallery_contents = json_decode($gallery->content);
            }


            $customhtml = Business::where('id', $business->id)->first();

            $days = business_hours::$days;
            $business_hours = '';
            if (!empty($businesshours->content)) {
                $business_hours = json_decode($businesshours->content);
            }
            if (json_decode($business->card_theme) == NULL) {
                $card_order = [];
                $card_order['theme'] = $business->card_theme;
                $card_order['order'] = Business::getDefaultThemeOrder($business->card_theme);
                $business->card_theme = json_encode($card_order);
                $business->save();
            }
            $card_theme = json_decode($business->card_theme);

            $PixelFields = PixelFields::where('business_id', $business->id)->get();
            $pixelScript = [];
            foreach ($PixelFields as $pixel) {

                if (!$pixel->disabled) {
                    $pixelScript[] = Business::pixelSourceCode($pixel['platform'], $pixel['pixel_id']);
                }
            }
            //Product
            $products = CardProduct::where('business_id', $business->id)->first();
            $products_content = [];
            if (!empty($products->content)) {
                $products_content = json_decode($products->content);
            }
            return view('vcard::card.' . $card_theme->theme . '.index', compact('businessfields', 'contactinfo', 'contactinfo_content', 'appoinment_hours', 'appoinment', 'services_content', 'services', 'testimonials_content', 'testimonials', 'social_content', 'sociallinks', 'customhtml', 'businesshours', 'business_hours', 'business', 'days', 'is_slug', 'is_pdf', 'gallery', 'gallery_contents', 'pixelScript', 'products', 'products_content'));
        } else {
            return abort('403', 'The Link You Followed Has Expired');
        }
    }

    public function getVcardDownload($slug)
    {
        $business = Business::where('slug', $slug)->first();
        $vcard = new VCard();
        $lastname = '';
        $firstname = $business->title;
        $additional = '';
        $prefix = '';
        $suffix = '';
        // add personal data
        $vcard->addName($lastname, $firstname, $additional, $prefix, $suffix);

        // add work data
        $vcard->addCompany($business->title);
        $vcard->addRole($business->designation);


        $cardLogo = isset($business->logo) && !empty($business->logo) ? get_file($business->logo) : asset('Modules/VCard/Resources/assets/custom/img/logo-placeholder-image-21.png');
        $cardLogoResponse = Http::get($cardLogo);
        if ($cardLogoResponse->successful()) {
            $cardLogoData = $cardLogoResponse->body();
            $localImagePath = public_path('card/card_logo.jpg'); // Include the desired filename in the path
            $directory = pathinfo($localImagePath, PATHINFO_DIRNAME);

            // Create the directory if it doesn't exist
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            file_put_contents($localImagePath, $cardLogoData);
            $vcard->addPhoto($localImagePath);

        }
        $contacts = ContactInfo::where('business_id', $business->id)->first();

        if (!empty($contacts) && !empty($contacts->content)) {
            if (isset($contacts['is_enabled']) && $contacts['is_enabled'] == '1') {
                $contact = json_decode($contacts->content, true);
                foreach ($contact as $key => $val) {
                    foreach ($val as $key2 => $val2) {
                        if ($key2 == 'Email') {
                            $vcard->addEmail($val2);
                        }
                        if ($key2 == 'Phone') {
                            $vcard->addPhoneNumber($val2, 'TYPE#WORK,VOICE');
                        }
                        if ($key2 == 'Whatsapp') {
                            $vcard->addPhoneNumber($val2, 'WORK');
                        }
                        if ($key2 == 'Web_url') {
                            $vcard->addURL($val2);
                        }
                    }
                }
            }
        }
        $sociallinks = Social::where('business_id', $business->id)->first();
        $social_content = [];
        if (!empty($sociallinks->content)) {
            $social_content = json_decode($sociallinks->content);
        }
        if (!is_null($social_content) && !is_null($sociallinks)) {
            if (isset($sociallinks['is_enabled']) && $sociallinks['is_enabled'] == '1') {
                foreach ($social_content as $social_key => $social_val) {
                    foreach ($social_val as $social_key1 => $social_val1) {
                        if ($social_key1 != 'id') {
                            $vcard->addURL($social_val1, 'TYPE=' . $social_key1);
                        }
                    }
                }
            }
        }

        $path = public_path('/card');

        \File::delete($path);
        if (!is_dir($path)) {
            \File::makeDirectory($path, 0777);
        }
        $vcard->setSavePath($path);

        $vcard->save();
        $file = $vcard->getFilename() . '.' . $vcard->getFileExtension();
        self::download($path . '/' . $file);

    }
    function download($file)
    {
        if (file_exists($file)) {
            $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
            header('Content-Description: File Transfer');
            if ($iPhone) {
                header('Content-Type: text/vcard');
            } else {
                header('Content-Type: application/octet-stream');
            }
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            flush();
            readfile($file);
            exit;
        }
    }




    public function ChangeStatus($id)
    {
        $business = Business::find($id);

        if ($business->status == 'locked') {
            $business->status = 'active';
            $business->save();
            event(new BusinessStatus($business));
            return redirect()->back()->with('success', __('Business unlock successfully'));
        } else {
            $business->status = 'locked';
            $business->save();
            event(new BusinessStatus($business));
            return redirect()->back()->with('success', __('Business lock successfully'));
        }


    }
    public function Grid(Request $request)
    {
        if (\Auth::user()->isAbleTo('business manage')) {
            $business = Business::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('id', 'DESC')->get();
            $no = 0;
            foreach ($business as $key => $value) {
                $value->no = $no;
                $no++;
            }
            return view('vcard::business.grid', compact('business'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function ChangeBusiness($id)
    {
        $business = Business::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
        foreach ($business as $value) {
            if ($id == $value->id) {
                $value->current_business = 1;
                $value->save();
            } else {
                $value->current_business = 0;
                $value->save();
            }

        }
        return redirect()->back();
    }

}