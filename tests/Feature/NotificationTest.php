<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    protected function loginAsUser(): User
    {
        $user = User::create([
            'email' => 'notif@test.com',
            'username' => 'notif_user',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'Employee', 'slug' => 'employee']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['employee'],
            'user_permissions' => [],
        ]);

        return $user;
    }

    public function test_can_view_notifications(): void
    {
        $user = $this->loginAsUser();
        $user->notify(new InAppNotification('Test message', 'info'));

        $response = $this->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Test message');
    }

    public function test_can_get_unread_count(): void
    {
        $user = $this->loginAsUser();
        $user->notify(new InAppNotification('Unread one', 'info'));

        $response = $this->getJson(route('notifications.unread-count'));

        $response->assertOk();
        $response->assertJson(['count' => 1]);
    }

    public function test_can_mark_all_notifications_read(): void
    {
        $user = $this->loginAsUser();
        $user->notify(new InAppNotification('One', 'info'));
        $user->notify(new InAppNotification('Two', 'info'));

        $response = $this->postJson(route('notifications.read-all'));

        $response->assertOk();
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}
