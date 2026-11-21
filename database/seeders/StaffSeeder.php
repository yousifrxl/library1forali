<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        Staff::updateOrCreate(
            ['email' => 'ali2@gmail.com'],
            ['name' => 'ali', 'password' => '12345678'],
        );
    }
}
