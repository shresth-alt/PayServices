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
    body {
      font-family: 'Segoe UI',sans-serif !important;
    }
    .padding {
      padding: 2rem !important
    }
    .card {
      margin-bottom: 30px;
      border: none;
      -webkit-box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22);
      -moz-box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22);
      box-shadow: 0px 1px 2px 1px rgba(154, 154, 204, 0.22)
    }
    .card-header {
      background-color: #fff;
      border-bottom: none;
    }

    h3 {
      font-size: 20px
    }

    h5 {
      font-size: 19px;
      line-height: 26px;
      color: #3d405c;
      margin: 0px 0px 15px 0px;
      font-family: 'Segoe UI',sans-serif  !important;
    }

    .text-dark {
      color: #3d405c !important;
    }
    .right {
      text-align: right;
    }
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: unset;
    }
    
    .driver-image img {
      max-width: 83px;
      min-width: 83px;
      max-height: 119px;
      min-height: 114px;
    }

    .driver-detail {
      font-size: 16px;
      font-weight: 400;
      color: black;
    }

    .driver-detail h3 {
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
    .service-category {
      font-size: 16px;
      font-weight: 400;
      color: black !important;
    }
    .sac {
      font-size: 16px;
      font-weight: 400;
      color: black;
    }
    .sac span {
      color: rgba(155, 155, 155, 1);
    }
    .service-category span {
      color: rgba(155, 155, 155, 1);
    }

    .table td {
      border-top: 2px solid rgba(155, 155, 155, 1);
      background-color: #fff;
    }
   
    .top-none {
      border-top: unset !important;
    }
    .coupon .bg-none {
      background-color: #fff;
    }
    .bill-head {
      font-size: 24px;
      font-weight: 600;
    }
    .trip-head {
      font-size: 17px;
      font-weight: 500;
      color: black;
    }
    .total-bill {
      font-size: 17px;
      font-weight: 500;
      color: black;
    }
    .coupon {
      font-size: 17px;
      font-weight: 500;
      color: rgba(112, 112, 112, 1);
      background-color: #fff;
    }
    .low-color {
      color: rgba(112, 112, 112, 1);
    }
    .main-heading-ola {
      display: flex;
    }
   
    .text-dark-black {
      color: black;
      font-size: 44px;
      font-weight: 600;
    }
    .queries-footer {
      border-top: none
    }
    .driver-detail-ex {
      display: flex;
    }
    .ola-icon {
      float: right;
    }
    .text-center-bill {
      text-align: center;
      margin-left: 95px;
    }
    .common-side-detail {
      display: flex;
    }
    .right-side-detail {
      float: right;
    }
    .left-side-detail {
      display: block;
    }
  </style>
</head>
<body>
  <div class="">
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
              <h3 class="text-dark service-category mb-1"><span>Service Tax Category:</span> Business Auxiliary Service</h3>
              <span class="sac"><span>SAC Code:</span> 999799</span>
          </div>
          </div>
        <div class="table-responsive-sm" style="text-align: center;">
          <h5 class="text-center bill-head">Bill Details</h5>
          <table class="table table-striped" width="100%">
            <tbody>
                <tr>
                    <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span style="color: rgba(155, 155, 155, 1)">Invoice ID </span>CRN{{ $order->id }}</td>
                    <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;"><span style="color: rgba(155, 155, 155, 1)">Invoice Date </span>{{$order->created_at->format('d/m/Y')}}</td>
                </tr>
                <tr>
                    <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span style="color: rgba(155, 155, 155, 1)">Customer Name</span> {{ $order->user->name }}</td>
                    <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;"><span style="color: rgba(155, 155, 155, 1)">Mobile Number</span> {{ $order->user->mobile }}</td>
                </tr>
                <tr>
                    <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;"><span>Billing Address</span> {{ $order->address }}</td>
                    <td class="right"></td>
                </tr>
                <tr>
                    <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">Description</td>
                    <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">Amount (INR)</td>
                </tr>
                
                <tr>
                    <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">PayServices Convenience Fee</td>
                    <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR {{$order->commission - $order->commission_gst}}</td>
                </tr>
                <tr>
                    <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">C GST 9.0%</td>
                    <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR {{number_format((float)($order->commission_gst)/2, 2, '.', '')}}</td>
                </tr>
                <tr>
                    <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">S GST 9.0%</td>
                    <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR {{number_format((float)($order->commission_gst)/2, 2, '.', '')}}</td>
                </tr>
                <tr>
                    <td class="center" style="padding-top: 10px; padding-bottom: 10px; padding-left: 4px;">Total</td>
                    <td class="right" style="padding-top: 10px; padding-bottom: 10px; padding-right: 4px;">INR {{$order->commission}}</td>
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