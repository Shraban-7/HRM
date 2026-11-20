<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\Auth\CustomAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DemoController extends Controller
{
    protected array $demoRoles = [
        'super_admin' => [
            'email' => 'admin@hrm.com',
            'username' => 'admin',
            'name' => 'Super Administrator',
            'role_slug' => 'super_admin',
        ],
        'hr_admin' => [
            'email' => 'hrmanager@hrm.com',
            'username' => 'hrmanager',
            'name' => 'HR Operations Lead',
            'role_slug' => 'hr_admin',
        ],
        'manager' => [
            'email' => 'manager@hrm.com',
            'username' => 'manager',
            'name' => 'Department Manager',
            'role_slug' => 'manager',
        ],
        'employee' => [
            'email' => 'user@hrm.com',
            'username' => 'employee1',
            'name' => 'Staff Employee',
            'role_slug' => 'employee',
        ],
    ];

    public function switchRole(string $role, CustomAuthService $authService, Request $request): RedirectResponse
    {
        if (! array_key_exists($role, $this->demoRoles)) {
            return redirect()->back()->with('error', 'Invalid demonstration role requested.');
        }

        $config = $this->demoRoles[$role];

        // Ensure user exists with fresh credentials
        $user = User::updateOrCreate(
            ['email' => $config['email']],
            [
                'username' => $config['username'],
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $roleModel = Role::where('slug', $config['role_slug'])->first();
        if ($roleModel && ! $user->roles()->where('role_id', $roleModel->id)->exists()) {
            $user->roles()->attach($roleModel->id);
        }

        // Login as demo user
        $authService->login($user->email, 'password');

        return redirect()->route('dashboard')->with('success', "Demo Mode: Switched to {$config['name']} session successfully.");
    }
}
