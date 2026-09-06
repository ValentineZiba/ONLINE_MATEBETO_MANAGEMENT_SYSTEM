<?php

namespace App\Providers;

use App\Services\Payments\Gateways\ManualGateway;
use App\Services\Payments\PaymentManager;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function () {
            $manager = new PaymentManager;
            $manager->register(new ManualGateway('cash'));
            $manager->register(new ManualGateway('card'));
            $manager->register(new ManualGateway('bank_transfer'));

            // Staff-attested for now (no real charge/verification) until a
            // real gateway (Flutterwave) is wired up for these networks.
            $manager->register(new ManualGateway('airtel_money'));
            $manager->register(new ManualGateway('mtn_momo'));
            $manager->register(new ManualGateway('zamtel_kwacha'));
            $manager->register(new ManualGateway('zampay'));

            return $manager;
        });
    }
}
