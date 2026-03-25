<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthTest extends TestCase
{
    use RefreshDatabase;
    public function test_health_endpoint_returns_ok_when_database_is_reachable(): void
    {
        $response = $this->getJson('/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'checks' => [
                    'database' => true,
                ],
            ]);
    }

    public function test_health_endpoint_returns_degraded_when_database_is_unavailable(): void
    {
        DB::shouldReceive('connection')->andThrow(new \Exception('Connection refused'));

        $response = $this->getJson('/health');

        $response->assertStatus(503)
            ->assertJson([
                'status' => 'degraded',
                'checks' => [
                    'database' => false,
                ],
            ]);
    }
}
