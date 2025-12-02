<?php

namespace Database\Seeders;

use App\Helpers\UserRoles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(5)->create();

        $user = User::factory()->create([
            'name' => 'Dafiewhare Emmanuel',
            'email' => 'emmadafi2@gmail.com',
        ]);

        foreach (UserRoles::cases() as $role) {
            Role::create(['name' => $role->value]);
        }

        $user->assignRole([UserRoles::Super->value, UserRoles::Admin->value]);
    }
}
