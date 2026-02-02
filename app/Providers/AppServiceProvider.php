<?php

namespace App\Providers;

use App\Services\Gateways\IyzicoGateway;
use App\Services\Gateways\StripeGateway;
use App\Services\PaymentGatewayFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayFactory::class, function ($app) {
            $factory = new PaymentGatewayFactory();

            $factory->register('stripe', $app->make(StripeGateway::class));
            $factory->register('iyzico', $app->make(IyzicoGateway::class));

            return $factory;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
