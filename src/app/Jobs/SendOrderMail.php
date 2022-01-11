<?php

namespace App\Jobs;

use App\Mail\OrdereMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * オーナ向けメールを送るためのjob
 */
class SendOrderMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product;
    public $user;

    public function __construct($product, $user)
    {
        $this->product = $product;
        $this->user = $user;
    }


    /**
     * job処理
     *
     * @return void
     */
    public function handle()
    {
        // メールの送信先は、オーナとし、メールの本文は別クラスで生成後に送信する
        Mail::to($this->product['email'])->send(new OrdereMail($this->product, $this->user));  
    }
}
