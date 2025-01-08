<?php

namespace App\Listeners;

use App\Events\OrderAccepted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\User;

class CustomerPushNotification
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
  * @param  OrderAccepted  $event
  * @return void
  */
  public function handle(OrderAccepted $event)
  {
    $order = $event->order;
    $message = "Your booking has been accepted by " . $order->vendor->name;
    $userId = $order->user_id;
    $title = "Booking has been accepted";
    $this->sendNotification($message, $userId, $title);
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
      'click_action' => "com.wki.payservies.OrderNotification",
      'order_notification' => true
    );
    $fields = array(
      'to' => $registrationIds,
      'notification'=> $msg,
      'data' => $msg
    );
    $headers = array(
      'Authorization: key=' . env('FIREBASE_ACCESS_KEY_CUSTOMER'),
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
