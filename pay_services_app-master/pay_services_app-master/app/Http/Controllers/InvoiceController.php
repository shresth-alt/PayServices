<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PDF;
use App\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Mail\InvoiceEmail;
use GuzzleHttp\Client;

class InvoiceController extends Controller {

  public function createInvoice(Request $request, $id) {
    $order = Order::find($id);
    $visiting_charge = env('VISITING_CHARGES');
    $pdf = PDF::loadView('admin.invoice', array('order' => $order, 'visiting_charge' => $visiting_charge));
    $pdf->save(public_path('pdf/'.'order_'.$order->id.'_invoice'.'.pdf'));
    return $pdf->download('order_'.$order->id.'_invoice'.'.pdf');
  }


  public function sendInvoice(Request $request, $id) {
    $order = Order::find($id);
    $visiting_charge = env('VISITING_CHARGES');
    $pdf = PDF::loadView('admin.invoice', array('order' => $order, 'visiting_charge' => $visiting_charge));
    $pdf->save(public_path('pdf/'.'order_'.$order->id.'_invoice'.'.pdf'));
    \Mail::to($order->billing_email)->send(new InvoiceEmail($order));
    /** SMS logic */
    $client = new Client(['verify' => false]);
    $customer_mobile = $order->user->mobile;
    $response = $client->request('POST', env('SMS_BASE_URL') . "/flow", array(
      'json' => array(
        'flow_id' => '5fd5aee4d6fc057a925bf48e',
        'sender' => env('SMS_SENDER'),
        'recipients' => array(array(
          'mobiles' => "91{$customer_mobile}",
          'VAR1' => $order->id,
          'VAR2' => $order->order_amount
        ))
      ),
      'headers' => array(
        'authkey' => env('SMS_AUTH_KEY')
      )
    ));
    $response = json_decode((string) $response->getBody(), true);
  }
  
}
