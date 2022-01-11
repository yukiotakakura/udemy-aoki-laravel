<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrdereMail extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $user;

    public function __construct($product, $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * メール本文
     * @return $this
     */
    public function build()
    {
        // メールの本文はbladeファイルで作成
        return $this->view('emails.ordered')
        // 件名
        ->subject('商品が注文されました。');
    }
}
