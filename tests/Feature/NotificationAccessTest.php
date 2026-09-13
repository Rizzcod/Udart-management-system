<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_cannot_mark_other_users_notifications_read(): void
    {
        [$owner, $other] = User::factory()->count(2)->create();
        $notification = Notification::create(['user_id' => $owner->id, 'title' => 'Assigned', 'message' => 'Work order assigned.', 'type' => 'info']);

        $this->actingAs($other)->patch("/notifications/{$notification->id}/read")->assertForbidden();
        $this->assertFalse($notification->fresh()->is_read);

        $this->actingAs($owner)->patch("/notifications/{$notification->id}/read")->assertRedirect();
        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_broadcast_notifications_can_be_marked_read_by_any_user(): void
    {
        $notification = Notification::create(['user_id' => null, 'title' => 'Low stock', 'message' => 'Brake pads low.', 'type' => 'warning']);

        $this->actingAs(User::factory()->create())->patch("/notifications/{$notification->id}/read")->assertRedirect();
        $this->assertTrue($notification->fresh()->is_read);
    }
}
