<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserPerRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'hr' => 'hr@example.com',
            'pm' => 'pm@example.com',
            'sekretaris' => 'sekretaris@example.com',
            'media' => 'media@example.com',
            'pr' => 'pr@example.com',
            'kewirausahaan' => 'kewirausahaan@example.com',
            'talent_manager' => 'talentmanager@example.com',
            'researcher' => 'researcher@example.com',
            'talent' => 'talent@example.com',
            'guest' => 'guest@example.com',
            'bendahara' => 'bendahara@example.com'
        ];

        foreach ($roles as $role => $email) {
            // ensure role exists
            Role::firstOrCreate(['name' => $role]);

            $user = User::updateOrCreate([
                'email' => $email
            ], [
                'name' => ucfirst(str_replace(['_','-'], ' ', $role)),
                'password' => bcrypt('password')
            ]);

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
