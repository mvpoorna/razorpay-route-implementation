@extends('layouts.app')

@section('content')
    <div class="">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Customer {{ $customer->id }}</div>
                    <div class="panel-body">

                        <a href="{{ url('admin/customers/' . $customer->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Customer"><span class="glyphicon glyphicon-pencil" aria-hidden="true"/></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['admin/customers', $customer->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<span class="glyphicon glyphicon-trash" aria-hidden="true"/>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Customer',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!}
                        @if(!$customer->virtual_account_id)
                        {!! Form::open([
                            'method'=>'POST',
                            'url' => ['admin/customer/virtualaccount', $customer->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('Create Virtual Account', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary pull-right btn-xs',
                                    'title' => 'Create Virtual Account',
                                    'onclick'=>'return confirm("Are you sure want to create virtual account?")'
                            ))!!}
                        {!! Form::close() !!}
                        @endif                        
                        <br><br>
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Customer Details</h3>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>ID</th><td>{{ $customer->id }}</td>
                                            </tr>
                                            <tr><th> Name </th><td> {{ $customer->name }} </td></tr><tr><th> Email </th><td> {{ $customer->email }} </td></tr><tr><th> About </th><td> {{ $customer->about }} </td></tr>
                                        </tbody>
                                    </table>
                            </div>
                            <div class="col-md-6">
                                <h3>Virtual Bank Information</h3>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Column</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bankinfo as $key => $value)
                                        <tr>
                                            <td>{{ $key }}</td>
                                            <td> {{ $value }} </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <h3>Payments</h3>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Entity</th>
                                            <th>Amount</th>
                                            <th>currency</th>
                                            <th>method</th>
                                            <th>Amount Refunded</th>
                                            <th>Email</th>
                                            <th>Fee</th>
                                            <th>GST</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $pa)
                                        <tr class="text-danger">
                                            <td> {{ $pa->id }} </td>
                                            <td> {{ $pa->entity }} </td>
                                            <td> {{ $pa->amount/100 }} </td>
                                            <td> {{ $pa->currency }} </td>
                                            <td> {{ $pa->method }} </td>
                                            <td> {{ $pa->amount_refunded/100 }} </td>
                                            <td> {{ $pa->email }} </td>
                                            <td> {{ $pa->fee/100 }} </td>
                                            <td> {{ $pa->tax/100 }} </td>
                                        </tr>
                                        <?php
                                            $api = new Razorpay\Api\Api('rzp_test_nh77prfo9r4ain', 'tVAPwYpbjCGxj792fthA9Rqs');
                                            $transfers = $api->payment->fetch($pa->id)->transfers();
                                        ?>
                                        <tr>
                                            <td colspan="9">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Entity</th>
                                                            <th>source</th>
                                                            <th>recipient</th>
                                                            <th>amount</th>
                                                            <th>currency</th>
                                                            <th>amount_reversed</th>
                                                            <th>fees</th>
                                                            <th>GST</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($transfers['items'] as $tr)
                                                        <tr>
                                                            <td> {{ $tr->id }} </td>
                                                            <td> {{ $tr->entity }} </td>
                                                            <td> {{ $tr->source }} </td>
                                                            <td> {{ $tr->recipient }} </td>
                                                            <td> {{ $tr->amount/100 }} </td>
                                                            <td> {{ $tr->currency }} </td>
                                                            <td> {{ $tr->amount_reversed }} </td>
                                                            <td> {{ $tr->fees/100 }} </td>
                                                            <td> {{ $tr->tax/100 }} </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection