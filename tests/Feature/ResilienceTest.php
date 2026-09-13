<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// No RefreshDatabase here: these tests take the database away on purpose, and the
// trait's rollback would then fail against the unreachable connection.
class ResilienceTest extends TestCase
{
    /** Point the app at a MySQL port nothing listens on, as during a database outage. */
    private function simulateDatabaseOutage(): void
    {
        config(['database.connections.outage' => array_merge(config('database.connections.mysql'), ['port' => 1])]);
        config(['database.default' => 'outage']);
        DB::purge('outage');
    }

    public function test_health_check_reports_database_ok(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertExactJson(['status' => 'ok', 'database' => 'ok']);
    }

    public function test_health_check_reports_database_outage_without_details(): void
    {
        $this->simulateDatabaseOutage();

        $this->getJson('/api/health')
            ->assertStatus(503)
            ->assertExactJson(['status' => 'unavailable', 'database' => 'unavailable']);
    }

    public function test_database_outage_shows_temporarily_unavailable_page(): void
    {
        $this->simulateDatabaseOutage();
        config(['session.driver' => 'database']); // pages read the session from MySQL

        $this->get('/login')
            ->assertStatus(503)
            ->assertHeader('Retry-After', '60')
            ->assertSee('Service temporarily unavailable. Please try again shortly.')
            ->assertDontSee('SQLSTATE');
    }

    public function test_database_outage_returns_json_503_to_api_clients(): void
    {
        $this->simulateDatabaseOutage();
        config(['session.driver' => 'database']);

        $this->getJson('/login')
            ->assertStatus(503)
            ->assertExactJson(['message' => 'Service temporarily unavailable. Please try again shortly.']);
    }
}
