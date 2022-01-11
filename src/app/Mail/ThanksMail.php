<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * ユーザ向けメール
 */
class ThanksMail extends Mailable
{
    use Queueable, SerializesModels;

    public $products;
    public $user;

    public function __construct($products, $user)
    {
        $this->products = $products;
        $this->user = $user;
    }

    /**
     * メール本文
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.thanks')->subject('ご購入ありがとうございます。');
    }
}
