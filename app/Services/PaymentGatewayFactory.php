<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use Exception;

class PaymentGatewayFactory
{
    private array $gateways = [];

    public function register(string $name, PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$name] = $gateway;
    }

    public function make(string $provider): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$provider])) {
            throw new Exception("Unsupported payment provider: {$provider}");
        }

        return $this->gateways[$provider];
    }
}
