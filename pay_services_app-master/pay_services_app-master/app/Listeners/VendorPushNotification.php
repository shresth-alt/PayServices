<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Order;
use App\Service;
use App\User;
use Illuminate\Support\Facades\DB;

class VendorPushNotification
{
  /**
  * Create the event listener.
  *
  * @return void
  */
  public function __construct()
  {
    //
  }
  
  /**
  * Handle the event.
  *
  * @param  OrderCreated  $event
  * @return void
  */
  public function handle(OrderCreated $event)
  {
    $order = $event->order;
    $service_id = $event->service_id;
    $postal_code = $event->postal_code;
    /** Fetching all vendors */
    $vendors = DB::table('vendor_services')
    ->join('users', 'vendor_services.vendor_id', '=', 'users.id')
    ->join('vendor_pincodes', 'vendor_pincodes.vendor_id', '=', 'users.id')
    ->where('vendor_services.service_id', $service_id)
    ->where('vendor_pincodes.pincode', $postal_code)
    ->select('users.*')
    ->get();
    $service = Service::find($service_id);
    foreach ($vendors as $vendor) {
      $this->sendNotification("You have received a new booking for {$service->name}!", $vendor->id, "New Booking Received");
    }
  }

  private function sendNotification($message, $userId, $title) 
  {
    $user = User::find($userId);
    $registrationIds = $user->device_token;
    $msg = array(
      'body' 	=> $message,
      'title'	=> $title,
      'icon'	=> 'myicon',
      'sound' => 'default',
      'click_action' => "com.wki.payserviesvendor.OrderNotification",
      'order_notification' => true
    );
    $fields = array(
      'to' => $registrationIds,
      'notification'=> $msg,
      'data' => $msg
    );
    $headers = array(
      'Authorization: key=' . env('FIREBASE_ACCESS_KEY'),
      'Content-Type: application/json'
    );
    #Send Reponse To FireBase Server	
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
  }
}
