@extends('admin.layouts.master')
@section("title") Order Details
@endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">Order Id  "{{ $id }}" Details </span>
      </h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
  </div>
</div>
<div class="content">
  <div class="card">
    <div class="card-body">
      <div class="table-responsive table-flex">
        <div class="col-md-8">
        <table class="table table-right-border mb-4 upper-table">
          <tbody>
          <tr>
          <!-- <td><span> <img src="https://res.cloudinary.com/vraio/image/upload/v1604642977/project8-720x520_b6tdch.jpg" class="card-img-top img-fluid" alt="..." /><b class="main-image-title">Heading</b></span>  </td>
          <td><span> <img src="https://res.cloudinary.com/vraio/image/upload/v1604642977/project8-720x520_b6tdch.jpg" class="card-img-top img-fluid" alt="..." /></span><b class="main-image-title-1">Heading</b>  </td> -->
          <td><span>  <a target="_blank" href="{{ asset('uploads/'.$order->image) }}"> <img src="{{ asset('uploads/'.$order->image) }}" class="card-img-top img-fluid" alt="..." /></span> </a><b class="main-image-title">Main Image</b>  </td>
          <td><span>  <a target="_blank" href="{{ asset('uploads/'.$order->serial_model_image) }}"> <img src="{{ asset('uploads/'.$order->serial_model_image) }}" class="card-img-top img-fluid" alt="..." /></span> </a><b class="main-image-title-1">Serial Model Image</b>  </td>
          
          </tr>
          </tbody>
        </table>
        <table class="table table-right-border">
          <thead>
            <th> Order Id </th>
            <th> Item Name </th>
            <th> Item Price</th>
            <th> Image</th>
          </thead>
          <tbody>
          @forelse ($items as $item)
          <tr>
          <td> {{$item->order_id}} </td>
          <td> {{$item->item_name}} </td>
          <td> {{$item->price}} </td>
          <td class="item-img">  <a target="_blank" href="{{ asset('uploads/'.$item->image) }}"><img
                      src="{{ asset('uploads/'.$item->image) }}" class="card-img-top img-fluid"
                      alt="..." /></a> </td>
          </tr>
          @empty
            <tr><td colspan="6" class="text-center">No records found</td></tr>
          @endforelse
          </tbody>
        </table>
        </div>
        <div class="col-md-4">
        <table class="table"> 
          <tbody>
            <tr>
              <td><span class="list-heading">Name : </span><span> {{$customer->customer_name}} </span></td>
            </tr>
            
            <tr>
              <td><span class="list-heading">Email : </span><span>@if (!empty($order->billing_email)) {{$order->billing_email}} @else {{$customer->user_email}} @endif</span></td>
            </tr>
            <tr>
              <td><span class="list-heading">Mobile : </span><span>@if(!empty($order->billing_alt_mobile)) {{$order->billing_alt_mobile}} @else (!empty($customer->alt_mobile)) {{$customer->alt_mobile}} @endif</span></td>
            </tr>
            @if (!empty($order->address))
            <tr>
              <td><span class="list-heading">Address: </span><span>{{$order->address}}</span></td>
            </tr>
            @endif
            <tr>
              <td><span class="list-heading">Village: </span><span>@if (!empty($order->billing_village))  {{ucfirst($order->billing_village)}}  @else {{ucfirst($customer->village)}} @endif</span></td>
            </tr>
            <tr>
              <td><span class="list-heading">Landmark: </span> <span>@if (!empty($order->billing_landmark)) {{ucfirst($order->billing_landmark)}}  @else  {{ucfirst($customer->landmark)}}  @endif</span></td>
            </tr>
            <tr>
              <td><span class="list-heading">City: </span><span>@if (!empty($order->billing_city))  {{ucfirst($order->billing_city)}}  @else {{ucfirst($customer->city)}}  @endif</span></td>
            </tr>
            <tr>
              <td><span class="list-heading">State: </span><span> @if (!empty($order->billing_state)) {{ucfirst($order->billing_state)}} @else {{ucfirst($customer->state)}} @endif</span></td>
            </tr>
            <tr>
              <td><span class="list-heading">Pincode: </span><span>@if (!empty($order->billing_pincode)) {{$order->billing_pincode}} @else {{$customer->pincode}} @endif</span></td>
            </tr>
          </tbody>
        </table>
      </div>
        <!-- <div>
        </div> -->
      </div>
    </div>
  </div>
</div>
</div>
@endsection
