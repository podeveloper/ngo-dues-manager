<?php

namespace Tests\Support\Stubs;

use App\Services\Gateways\StripeGateway;

class StripeGatewayStub extends StripeGateway
{
    protected function createCharge(array $payload): array
    {
        return array_merge(['id' => 'ch_test_123'], $payload);
    }
}
