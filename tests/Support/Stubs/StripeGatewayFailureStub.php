<?php

namespace Tests\Support\Stubs;

use App\Services\Gateways\StripeGateway;
use Exception;

class StripeGatewayFailureStub extends StripeGateway
{
    protected function createCharge(array $payload): array
    {
        throw new Exception('No API key provided');
    }
}
