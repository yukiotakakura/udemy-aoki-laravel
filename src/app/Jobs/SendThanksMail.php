<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Mail\ThanksMail;

/**
 * ユーザ向けメールを送るためのjob
 */
class SendThanksMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 購入商品
    public $products;

    // ユーザ
    public $user;

    /**
     * Undocumented function
     *
     * @param [type] $products
     * @param [type] $user
     */
    public function __construct($products, $user)
    {
        $this->products = $products;
        $this->user = $user;
    }

    /**
     * job処理
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user)->send(new ThanksMail($this->products, $this->user));
    }
}
