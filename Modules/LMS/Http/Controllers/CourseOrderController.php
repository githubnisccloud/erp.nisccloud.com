<?php

namespace Modules\LMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\Entities\CourseOrder;
use Modules\LMS\Entities\Store;
use Modules\LMS\Entities\Student;
use Illuminate\Support\Facades\Crypt;

class CourseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $user  = Auth::user();
        $store = Store::where('workspace_id',getActiveWorkSpace())->where('created_by',creatorId())->first();

        $Course_orders = CourseOrder::orderBy('id', 'DESC')->where('store_id', $store->id)->get();

        return view('lms::course_orders.index', compact('Course_orders'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('lms::create');
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
        if(\Auth::user()->isAbleTo('course order show'))
        {
            $courseorder = CourseOrder::find($id);
            $store = Store::where('id', $courseorder->store_id)->first();

            $order_products = json_decode($courseorder->course);
            $sub_total = 0;
            if(!empty($order_products))
            {
                foreach($order_products as $product)
                {
                    $totalprice = $product->price;
                    $sub_total  += $totalprice;
                }
            }
            if(!empty($store->currency)){
                $currency = $store->currency;
            }else{
                $currency = '$';
            }

            if($courseorder->discount_price == 'undefined'){
                $discount_price = 0;
            }else{
                $discount_price = str_replace('-' . $currency, '', $courseorder->discount_price);
            }

            if(!empty($discount_price))
            {
                $grand_total = $sub_total - $discount_price;
            }
            else
            {
                $discount_price = 0;
                $grand_total    = $sub_total;
            }
            $student_data = Student::where('id', $courseorder->student_id)->first();
            $order_id     = Crypt::encrypt($courseorder->id);


            return view('lms::course_orders.view', compact('student_data', 'discount_price', 'courseorder', 'store', 'grand_total', 'order_products', 'sub_total', 'order_id'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('lms::edit');
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
        if(\Auth::user()->isAbleTo('course order delete'))
        {
            $course_order = CourseOrder::find($id);
            $course_order->delete();
            return redirect()->back()->with('success', __('Course Order Deleted!'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
