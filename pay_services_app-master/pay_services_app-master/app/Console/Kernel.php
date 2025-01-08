<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Order;
use GuzzleHttp\Client;

class Kernel extends ConsoleKernel
{
  /**
  * The Artisan commands provided by your application.
  *
  * @var array
  */
  protected $commands = [
    //
  ];
  
  /**
  * Define the application's command schedule.
  *
  * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
  * @return void
  */
  protected function schedule(Schedule $schedule) {
    $schedule->call(function() {
      $order = Order::where('invoice_sent', false)->where('status', 'completed')->whereNotNull('billing_email')->first();
      if ($order != null) {
        $client = new Client(['verify' => false]);
        $client->request('GET', "https://app.payservice.in/send-invoice/{$order->id}");
        $order->invoice_sent = true;
        $order->update();
      }
    })->everyMinute();
  }
  
  /**
  * Register the commands for the application.
  *
  * @return void
  */
  protected function commands()
  {
    $this->load(__DIR__.'/Commands');
    require base_path('routes/console.php');
  }
}
