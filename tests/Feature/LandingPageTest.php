<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_see_the_project_landing_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('UDART Maintenance Management System')
            ->assertSee('Raphael Joseph')
            ->assertSee(route('login'));
    }

    public function test_signed_in_users_are_sent_to_their_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }

    public function test_demo_credentials_are_hidden_unless_demo_mode_is_enabled(): void
    {
        config(['demo.enabled' => false]);

        foreach (config('demo.accounts') as $account) {
            $this->get('/')->assertDontSee($account['email']);
            $this->get('/login')->assertDontSee($account['email']);
        }
    }

    public function test_demo_credentials_are_listed_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);

        $landing = $this->get('/')->assertOk();
        $login   = $this->get('/login')->assertOk();

        foreach (config('demo.accounts') as $account) {
            $landing->assertSee($account['email'])->assertSee(route('login', ['as' => $account['key']]));
            $login->assertSee($account['email']);
        }
    }
}
