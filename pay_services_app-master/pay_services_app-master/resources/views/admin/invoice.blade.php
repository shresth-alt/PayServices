<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
    @font-face {
      font-family: SegoeUI;
      src:
        local("Segoe UI"),
        url(public/latest.woff2) format("woff2"),
        url(public/latest.woff) format("woff"),
        url(public/latest.ttf) format("truetype");
      font-weight: 400;
    }
    footer {
      position: fixed; 
      bottom: -22px; 
      left: 0px; 
      right: 0px;
      height: 30px; 
      line-height: 5px;
    }
    .text-center {
      text-align: center;  
    }
    .invoice-first body {
      font-family: 'Segoe UI', sans-serif !important;
    }

    .invoice-first .padding {
      padding: 2rem !important
    }

    .invoice-first .card {
      margin-bottom: 30px;
      border: none;
      -webkit-box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22);
      -moz-box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22);
      box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22)
    }

    .invoice-first .card-header {
      background-color: #fff;
      border-bottom: none;
    }

    .invoice-first h3 {
      font-size: 20px
    }

    .invoice-first h5 {
      font-size: 15px;
      line-height: 26px;
      color: #3d405c;
      margin: 0px 0px 15px 0px;
      font-family: 'Segoe UI', sans-serif !important;
    }

    .invoice-first .text-dark {
      color: #3d405c !important;
    }

    .invoice-first .right {
      text-align: right;
    }

    .invoice-first .table-striped tbody tr:nth-of-type(odd) {
      background-color: unset;
    }

    .invoice-first .driver-full-detail {
      display: block;
    }

    .invoice-first .driver-image {
      display: flex;
    }

    .invoice-first .driver-image img {
      max-width: 83px;
      min-width: 83px;
      max-height: 119px;
      min-height: 114px;
    }

    .invoice-first .driver-detail {
      border-bottom: 4px solid rgba(236, 236, 236, 1);
      font-size: 16px;
      font-weight: 400;
      color: black;
    }

    .invoice-first .driver-detail h3 {
      position: relative;
      line-height: 1.6;
    }

    .invoice-first .driver-detail h3 .right-line {
      position: absolute;
      border: 1px solid rgba(205, 205, 205, 1);
      width: 72px;
      left: 332px;
      bottom: -7px;
    }

    .invoice-first .driver-detail h3 .left-line {
      position: absolute;
      border: 1px solid rgba(205, 205, 205, 1);
      width: 72px;
      right: 332px;
      bottom: -7px;
    }

    .invoice-first .service-category {
      font-size: 16px;
      font-weight: 400;
      color: black !important;
    }

    .invoice-first .sac {
      font-size: 16px;
      font-weight: 400;
      color: black;
    }

    .invoice-first .sac span {
      color: rgba(155, 155, 155, 1);
    }

    .invoice-first .service-category span {
      color: rgba(155, 155, 155, 1);
    }

    .invoice-first .table td {
      border-top: 2px solid rgba(155, 155, 155, 1);
      background-color: rgba(243, 243, 243, 1);
    }

    .invoice-first .top-none {
      border-top: unset !important;
    }

    .invoice-first .coupon .bg-none {
      background-color: #fff;
    }

    .invoice-first .bill-head {
      font-size: 24px;
      font-weight: 600;
      margin-top: 12px;
    }

    .invoice-first .trip-head {
      font-size: 17px;
      font-weight: 500;
      color: black;
    }

    .invoice-first .total-bill {
      font-size: 17px;
      font-weight: 500;
      color: black;
    }

    .invoice-first .coupon {
      font-size: 17px;
      font-weight: 500;
      color: rgba(112, 112, 112, 1);
      background-color: #fff;
    }

    .invoice-first .low-color {
      color: rgba(112, 112, 112, 1);
    }

    .invoice-first .main-heading-ola {
      display: flex;
    }

    .invoice-first .text-dark-black {
      color: black;
      font-size: 44px;
      font-weight: 600;
    }

    .invoice-first .queries-footer {
      border-top: none
    }

    .invoice-first .driver-detail-ex {
      display: flex;
    }

    .invoice-first .ola-icon {
      float: right;
    }

    .invoice-first .ola-icon img {
      width: 65px;
    }


    .page-break {
      page-break-after: always;
    }

    /** */

    .invoice-second body {
      font-family: 'Segoe UI', sans-serif !important;
    }

    .invoice-second .padding {
      padding: 2rem !important
    }

    .invoice-second .card {
      margin-bottom: 30px;
      border: none;
      -webkit-box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22);
      -moz-box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22);
      box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22)
    }

    .invoice-second .card-header {
      background-color: #fff;
      border-bottom: none;
    }

    .invoice-second h3 {
      font-size: 20px
    }

    .invoice-second h5 {
      font-size: 19px;
      line-height: 26px;
      color: #3d405c;
      margin: 0px 0px 15px 0px;
      font-family: 'Segoe UI', sans-serif !important;
    }

    .invoice-second .text-dark {
      color: #3d405c !important;
    }

    .invoice-second .right {
      text-align: right;
    }

    .invoice-second .table-striped tbody tr:nth-of-type(odd) {
      background-color: unset;
    }

    .invoice-second .driver-full-detail {
      display: flex;
    }

    .invoice-second .driver-image {
      display: flex;
    }

    .invoice-second .driver-image img {
      max-width: 83px;
      min-width: 83px;
      max-height: 119px;
      min-height: 114px;
    }

    .invoice-second .driver-detail {
      font-size: 16px;
      font-weight: 400;
      color: black;
      margin-left: 17%;
    }

    .invoice-second .driver-detail h3 {
      position: relative;
      line-height: 0.1;
    }

    .invoice-second .driver-detail h3 .right-line {
      position: absolute;
      border: 1px solid rgba(205, 205, 205, 1);
      width: 72px;
      left: 332px;
      bottom: -7px;
    }

    .invoice-second .driver-detail h3 .left-line {
      position: absolute;
      border: 1px solid rgba(205, 205, 205, 1);
      width: 72px;
      right: 332px;
      bottom: -7px;
    }

    .invoice-second .service-category {
      font-size: 16px;
      font-weight: 400;
      color: black !important;
    }

    .invoice-second .sac {
      font-size: 16px;
      font-weight: 400;
      color: black;
    }

    .invoice-second .sac span {
      color: rgba(155, 155, 155, 1);
    }

    .invoice-second .service-category span {
      color: rgba(155, 155, 155, 1);
    }

    .invoice-second .table td {
      border-top: 2px solid rgba(155, 155, 155, 1);
      background-color: #fff;
    }

    .invoice-second .top-none {
      border-top: unset !important;
    }

    .invoice-second .coupon .bg-none {
      background-color: #fff;
    }

    .invoice-second .bill-head {
      font-size: 24px;
      font-weight: 600;
    }

    .invoice-second .trip-head {
      font-size: 17px;
      font-weight: 500;
      color: black;
    }

    .invoice-second .total-bill {
      font-size: 17px;
      font-weight: 500;
      color: black;
    }

    .invoice-second .coupon {
      font-size: 17px;
      font-weight: 500;
      color: rgba(112, 112, 112, 1);
      background-color: #fff;
    }

    .invoice-second .low-color {
      color: rgba(112, 112, 112, 1);
    }

    .invoice-second .main-heading-ola {
      display: flex;
    }

    .invoice-second .text-dark-black {
      color: black;
      font-size: 44px;
      font-weight: 600;
    }

    .invoice-second .queries-footer {
      border-top: none
    }

    .invoice-second .driver-detail-ex {
      display: flex;
    }

    .invoice-second .ola-icon {
      float: right;
    }

    .invoice-second .text-center-bill {
      text-align: center;
    }

    .invoice-third body {
      font-family: 'Segoe UI', sans-serif !important;
    }

    .invoice-third .padding {
      padding: 2rem !important
    }

    .invoice-third .card {
      margin-bottom: 30px;
      border: none;
      -webkit-box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22);
      -moz-box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22);
      box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22)
    }

    .invoice-third .card-header {
      background-color: #fff;
      border-bottom: none;
    }

    .invoice-third h3 {
      font-size: 20px
    }

    .invoice-third h5 {
      font-size: 19px;
      line-height: 26px;
      color: #3d405c;
      margin: 0px 0px 15px 0px;
      font-family: 'Segoe UI', sans-serif !important;
    }

    .invoice-third .text-dark {
      color: #3d405c !important;
    }

    .invoice-third .right {
      text-align: right;
    }

    .invoice-third .table-striped tbody tr:nth-of-type(odd) {
      background-color: unset;
    }

    .invoice-third .driver-image img {
      max-width: 83px;
      min-width: 83px;
      max-height: 119px;
      min-height: 114px;
    }

    .invoice-third .driver-detail {
      font-size: 16px;
      font-weight: 400;
      color: black;
    }

    .invoice-third .driver-detail h3 {
      position: relative;
      line-height: 1px;
    }

    /* .driver-detail h3 .right-line {
      position: absolute;
      border: 1px solid rgba(205, 205, 205, 1);
      width: 72px;
      left: 332px;
      bottom: -7px;
    }
    .driver-detail h3 .left-line {
      position: absolute;
      border: 1px solid rgba(205, 205, 205, 1);
      width: 72px;
      right: 332px;
      bottom: -7px;
    } */
    .invoice-third .service-category {
      font-size: 16px;
      font-weight: 400;
      color: black !important;
    }

    .invoice-third .sac {
      font-size: 16px;
      font-weight: 400;
      color: black;
    }

    .invoice-third .sac span {
      color: rgba(155, 155, 155, 1);
    }

    .invoice-third .service-category span {
      color: rgba(155, 155, 155, 1);
    }

    .invoice-third .table td {
      border-top: 2px solid rgba(155, 155, 155, 1);
      background-color: #fff;
    }

    .invoice-third .top-none {
      border-top: unset !important;
    }

    .invoice-third .coupon .bg-none {
      background-color: #fff;
    }

    .invoice-third .bill-head {
      font-size: 24px;
      font-weight: 600;
    }

    .invoice-third .trip-head {
      font-size: 17px;
      font-weight: 500;
      color: black;
    }

    .invoice-third .total-bill {
      font-size: 17px;
      font-weight: 500;
      color: black;
    }

    .invoice-third .coupon {
      font-size: 17px;
      font-weight: 500;
      color: rgba(112, 112, 112, 1);
      background-color: #fff;
    }

    .invoice-third .low-color {
      color: rgba(112, 112, 112, 1);
    }

    .invoice-third .main-heading-ola {
      display: flex;
    }

    .invoice-third .text-dark-black {
      color: black;
      font-size: 44px;
      font-weight: 600;
    }

    .invoice-third .queries-footer {
      border-top: none
    }

    .invoice-third .driver-detail-ex {
      display: flex;
    }

    .invoice-third .ola-icon {
      float: right;
    }

    .invoice-third .text-center-bill {
      text-align: center;
      margin-left: 95px;
    }

    .invoice-third .common-side-detail {
      display: flex;
    }

    .invoice-third .right-side-detail {
      float: right;
    }

    .invoice-third .left-side-detail {
      display: block;
    }
  </style>
</head>

<body>
<footer>
<div class="text-center">
  <p class="text-center"> SUBJECT TO PALI RAJASTHAN JURISDICTION </p>
  <p class="text-center"> This is a Computer Generated Invoice </p>
</div>
</footer>
  <div class="invoice-first">
    <div class="card">
      <div class="card-header p-4">
        <div class="main-heading-ola">
          <div class="ola-date">
            <h3 class="mb-0">{{ $order->created_at->format('d/m/Y') }} </h3>
          </div>
          <div class="ola-icon">
            <img src="{{ public_path('pay-logo.png') }}">
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="">
          <div class="">
            <div class="driver-detail" style="text-align: center;">
              <h3 class="text-dark-black text-center">INR {{ $order->order_amount }}</h3>
              <h3 class="text-dark text-center">CRN{{$order->invoice_no}}</h3>
              <h3 class="text-dark text-center">Thanks for choosing us, {{$order->user->name}}</h3>
            </div>
          </div>
        </div>
        <div class="table-responsive-sm" style="text-align: center;">
          <h5 class="text-center bill-head">Bill Details</h5>
          <table class="table table-striped" width="100%">
            <tbody>
              <tr class="trip-head">
                <td class="center" style="padding-top: 12px; padding-bottom: 12px; padding-left: 6px;"><span>Total
                    Charge</span></td>
                <td class="right" style="padding-top: 12px; padding-bottom: 12px; padding-right: 6px;"><span>INR
                    {{ $order->order_amount }}</span></td>
              </tr>
              <tr class="total-bill">
                <td class="center top-none" style="padding-top: 12px; padding-bottom: 12px; padding-left: 6px;">
                  <span>Total Bill </td>
                <td class="right top-none" style="padding-top: 12px; padding-bottom: 12px; padding-right: 6px;">INR
                  {{ $order->order_amount }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="card-footer bg-white queries-footer">
          <p class="mb-0">Have queries? Mail us! support@payservice.in</p>
          <p class="mb-0">We've fulfilled our commitment to<br> give you a satisfactory service as pre-agreed.<br> Feel
            free to contact us for any trouble.</p>
        </div>

        <div class="row mb-4">
          <div class="col-sm-12">
            <div class="driver-full-detail">
              <div class="driver-image">

                <h3 class="text-dark"> {{$order->vendor->name}} </h3>
              </div>
              <div class="driver-detail-ex">
                <div class="bike-icon">
                  <i class="icon-bike"></i>
                </div>
                <!-- <span>Ola Bike, Maestro</span> -->
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="page-break"> </div>

  <div class="invoice-second">
    <div class="card">
      <div class="card-header p-4">
        <div class="text-center-bill">
          <h3 class="mb-0">Bill of Supply</h3>
        </div>
      </div>
      <div class="card-body">
        <div>
          <h5 class="mb-3 invoice-head">Technician Service Invoice</h5>
          <div class="driver-full-detail">
            <div class="driver-image">
              <img src="{{ public_path('avatar1.jpg') }}">
            </div>

            <div class="driver-detail">
              <h3 class="text-dark">{{ $order->vendor->name }}</h3>
              <span>{{ $order->vendor->detail->per_address }}</span><br>
              <span>PAY{{$order->vendor->id}}</span><br>
              <span>Operator State/UT:</span><br>
              <span> {{ucfirst($order->vendor->detail->state)}} </span>
            </div>
          </div>
        </div>
        <div class="table-responsive-sm" style="text-align: center;">
          <h5 class="text-center bill-head">Bill Details</h5>
          <table class="table table-striped" width="100%">
            <tbody>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span
                    style="color: rgba(155, 155, 155, 1)">Invoice ID </span>CRN{{ $order->invoice_no }}</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;"><span
                    style="color: rgba(155, 155, 155, 1)">Invoice Date </span> {{$order->created_at->format('d/m/Y')}}
                </td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span
                    style="color: rgba(155, 155, 155, 1)">Customer Name</span> {{ $order->user->name }} </td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;"><span
                    style="color: rgba(155, 155, 155, 1)">Mobile Number</span> {{ $order->user->mobile }}</td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span>Billing
                    Address:</span> {{ $order->address }}</td>
                <td class="right"></td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">Description</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">Amount (INR)</td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">
                  {{ $order->charge_type == 'service' ? 'Service Charge' : 'Visiting Charge' }}</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR 
                  {{ $order->charge_type == 'service' ? $order->service->charges : $visiting_charge }}</td>
              </tr>
              @foreach ($order->items as $item)
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">Item -
                  {{ucfirst($item->item_name)}} </td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR
                  {{$item->price}}</td>
              </tr>
              @endforeach
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">Total</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR
                  {{$order->order_amount}}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card-footer bg-white">
      <p class="mb-0">Please note: 1. This document is issued by the Service Provider and not by Pay Services. Pay
        Services acts only as an Electronic Commerce Operator for the services mentioned. GST is exempt from the
        Serviceman invoice of the Service/repairing.</p>
    </div>
  </div>
  <div class="page-break"></div>
  
  <div class="invoice-third">
    <div class="card">
      <div class="card-header" style="margin-bottom: 18px;">
        <div class="text-center-bill">
          <h3 class="mb-0">Original Tax Invoice</h3>
        </div>
      </div>
      <div class="card-body">
        <div class="common-side-detail">
          <div class="left-side-detail">
            <div class="driver-full-detail">
              <div class="driver-image">
                <img src="{{ public_path('pay-logo.png') }}">
              </div>
              <div class="driver-detail">
                <h3 class="text-dark">Pay Services</h3>
                <span>Office: 33, Mill Society,</span><br>
                <span>Subhash Nagar B,</span><br>
                <span>Pali-Marwar,</span><br>
                <span>Rajasthan 306401</span>
              </div>
            </div>
          </div>

          <div class="right-side-detail">
            <h5 class="mb-3"><span>State GST:</span> 08MQIPS5835B1ZD</h5>
            <h3 class="text-dark service-category mb-1"><span>Service Tax Category:</span> Business Auxiliary Service
            </h3>
            <span class="sac"><span>SAC Code:</span> 999799</span>
          </div>
        </div>
        <div class="table-responsive-sm" style="text-align: center;">
          <h5 class="text-center bill-head">Bill Details</h5>
          <table class="table table-striped" width="100%">
            <tbody>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span
                    style="color: rgba(155, 155, 155, 1)">Invoice ID </span>CRN{{ $order->invoice_no }}</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;"><span
                    style="color: rgba(155, 155, 155, 1)">Invoice Date </span>{{$order->created_at->format('d/m/Y')}}
                </td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span
                    style="color: rgba(155, 155, 155, 1)">Customer Name</span> {{ $order->user->name }}</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;"><span
                    style="color: rgba(155, 155, 155, 1)">Mobile Number</span> {{ $order->user->mobile }}</td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span>Billing
                    Address</span> {{ $order->address }}</td>
                <td class="right"></td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">Description</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">Amount (INR)</td>
              </tr>

              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">PayServices
                  Convenience Fee</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR
                  {{$order->commission - $order->commission_gst}}</td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">C GST 9.0%</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR
                  {{number_format((float)($order->commission_gst)/2, 2, '.', '')}}</td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">S GST 9.0%</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR
                  {{number_format((float)($order->commission_gst)/2, 2, '.', '')}}</td>
              </tr>
              <tr>
                <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">Total</td>
                <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR
                  {{$order->commission}}</td>
              </tr>
              <tr>
                <td class="center">
                  Authorised Signatory
                  <span></span>
                </td>
                <td class="right"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>

</html>