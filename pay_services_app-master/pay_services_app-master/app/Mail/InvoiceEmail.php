<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Order;

class InvoiceEmail extends Mailable
{
  use Queueable, SerializesModels;
  
  public $order;

  /**
  * Create a new message instance.
  *
  * @return void
  */
  public function __construct(Order $order) {
    $this->order = $order;
  }
  
  /**
  * Build the message.
  *
  * @return $this
  */
  public function build() {
    return $this->subject("Invoice")
    ->view('emails.invoice')
    ->attach(public_path("pdf/order_{$this->order->id}_invoice.pdf"))
    ->from("support@payservice.in", "Pay Service");
  }
}
