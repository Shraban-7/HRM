<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'shift@test.com',
            'username' => 'shift_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['attendance.manage'],
        ]);
    }

    public function test_can_list_shifts(): void
    {
        $this->loginAsAdmin();
        Shift::create([
            'name' => 'Morning',
            'slug' => 'morning',
            'start_time' => '08:00',
            'end_time' => '16:00',
            'is_active' => true,
        ]);

        $response = $this->get(route('shifts.index'));

        $response->assertStatus(200);
        $response->assertSee('Morning');
    }

    public function test_can_create_shift(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('shifts.store'), [
            'name' => 'Night',
            'start_time' => '22:00',
            'end_time' => '06:00',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shifts', ['name' => 'Night', 'is_active' => true]);
    }

    public function test_can_delete_shift(): void
    {
        $this->loginAsAdmin();
        $shift = Shift::create([
            'name' => 'Evening',
            'slug' => 'evening',
            'start_time' => '14:00',
            'end_time' => '22:00',
        ]);

        $response = $this->delete(route('shifts.destroy', $shift));

        $response->assertRedirect();
        $this->assertDatabaseMissing('shifts', ['id' => $shift->id]);
    }
}
