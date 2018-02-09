<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Customer;
use Illuminate\Http\Request;
use Session;
use Razorpay\Api\Api;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customers = Customer::paginate(25);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $api = new Api('rzp_test_nh77prfo9r4ain', 'tVAPwYpbjCGxj792fthA9Rqs');

        $requestData = $request->all();
        
        $customer = $api->customer->create(array('name' => $requestData['name'], 'email' => $requestData['email']));
        $requestData['razorpay_id'] = $customer->id;
        $virtualAccount  = $api->virtualAccount->create(array('receiver_types' => array('bank_account'), 'description' => 'First Virtual Account','customer_id' => $requestData['razorpay_id'], 'notes' => array('receiver_key' => 'receiver_value')));
        Customer::create($requestData);
        Session::flash('flash_message', 'Customer added!');

        return redirect('admin/customers');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $api            = new Api('rzp_test_nh77prfo9r4ain', 'tVAPwYpbjCGxj792fthA9Rqs');
        $customer       = Customer::findOrFail($id);
        $virtualAccount = $api->virtualAccount->fetch($customer->virtual_account_id);
        $bankinfo       = count($virtualAccount) ? $virtualAccount['receivers'][0] : [];
        $payment        = count($virtualAccount) ? $virtualAccount->payments() : [];
        $payments       = count($payment) ? $payment['items'] : [];
        //$transfers      = $api->payment->fetch('pay_9ZLnys9dI4WhGG')->transfers();
        //dd($transfers);
        return view('admin.customers.show', compact('customer','bankinfo','payments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        $api = new Api('rzp_test_nh77prfo9r4ain', 'tVAPwYpbjCGxj792fthA9Rqs');
        $requestData = $request->all();
        
        $customer1 = Customer::findOrFail($id);
        $customer = $api->customer->edit(array('name' => $requestData['name'], 'email' => $requestData['email']));
        $customer1->update($requestData);
        Session::flash('flash_message', 'Customer updated!');

        return redirect('admin/customers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Customer::destroy($id);

        Session::flash('flash_message', 'Customer deleted!');

        return redirect('admin/customers');
    }

    public function createVirtualAccount($id){
        $customer       = Customer::findOrFail($id);
        $api            = new Api('rzp_test_nh77prfo9r4ain', 'tVAPwYpbjCGxj792fthA9Rqs');
        $virtualAccount = $api->virtualAccount->create(array('receiver_types' => array('bank_account'), 'description' => 'First Virtual Account', 'customer_id' => $customer->razorpay_id, 'notes' => array('receiver_key' => 'receiver_value')));
        $customer->virtual_account_id = $virtualAccount->id;
        $customer->save();
        return redirect('admin/customers/'.$id);

    }
}
