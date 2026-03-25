<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok_when_database_is_reachable(): void
    {
        DB::shouldReceive('connection->getPdo')->once()->andReturn(new \stdClass());

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
        DB::shouldReceive('connection->getPdo')->once()->andThrow(new \Exception('Connection refused'));

        $response = $this->getJson('/health');

        $response->assertStatus(503)
            ->assertJson([
                'status' => 'degraded',
                'checks' => [
                    'database' => false,
                ],
            ]);
    }

    public function test_health_endpoint_is_not_wrapped_in_web_middleware(): void
    {
        $route = Route::getRoutes()->getByName('health');

        $this->assertNotNull($route);
        $this->assertSame([], $route->gatherMiddleware());
    }
}
