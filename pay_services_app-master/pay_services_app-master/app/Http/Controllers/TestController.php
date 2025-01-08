<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestController extends Controller {

  public function index() {
    Log::info('Hekki');
  }
  public function invoice_no () {
    $invoice_no = DB::table('orders')->max('invoice_no');
    $increment_invoice_no = $invoice_no + 1;
    
    print_r($increment_invoice_no);
    exit;
   }
}
