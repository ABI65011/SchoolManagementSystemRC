<?php

namespace Database\Seeders;

use App\Helpers\Classes as HelpersClasses;
use App\Helpers\RecurringPattern;
use App\Helpers\UserRoles;
use App\Models\Classes;
use App\Models\holidayCalendar;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $holidays = [

            [
                'name' => 'New Year\'s Day',
                'date' => '2026-02-01',
                'type' => 'Public Holiday',
                'is_recurring' => true,
                'recurring_pattern' => array_key_exists('Yearly', array_column(RecurringPattern::cases(), 'value')) ? 'Yearly' : 'yearly',
                'recurring_rules' => json_encode(['month' => 2, 'day' => 1]),
            ],
            [
                'name' => 'Independence Day',
                'date' => '2026-02-09',
                'type' => 'National Holiday',
                'is_recurring' => true,
                'recurring_pattern' => array_key_exists('Yearly', array_column(RecurringPattern::cases(), 'value')) ? 'Yearly' : 'yearly',
                'recurring_rules' => json_encode(['month' => 2, 'day' => 9]),
            ],


            [
                'name' => 'Labour Day',
                'date' => '2026-05-01',
                'type' => 'Public Holiday',
                'is_recurring' => true,
                'recurring_pattern' => 'yearly',
                'recurring_rules' => json_encode(['month' => 5, 'day' => 1]),
            ],


            [
                'name' => 'Good Friday',
                'date' => '2026-04-17',
                'type' => 'Religious Holiday',
                'is_recurring' => true,
                'recurring_pattern' => 'Easter Based',
                'recurring_rules' => json_encode(['days_offset' => -2]),
            ],
            [
                'name' => 'Easter Monday',
                'date' => '2026-04-21',
                'type' => 'Religious Holiday',
                'is_recurring' => true,
                'recurring_pattern' => 'Easter Based',
                'recurring_rules' => json_encode(['days_offset' => 1]),
            ],
        ];

        foreach ($holidays as $holiday) {
            holidayCalendar::create($holiday);
        }

        $user = User::factory()->create([
            'name' => 'Sandy Brown',
            'email' => 'sandybrown@gmail.com',
        ]);
        $user->assignRole(UserRoles::HR->value);

        // $classNames = array_column(HelpersClasses::cases(), 'value');

        // foreach ($classNames as $name) {
        //     DB::table('classes')->updateOrInsert(
        //         ['name' => $name],
        //         ['created_at' => now(), 'updated_at' => now()]
        //     );
        // }
    }
}
