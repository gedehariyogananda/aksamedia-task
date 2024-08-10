<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // sedder for Division
        $divisions = ['Mobile Apps', 'QA', 'Full Stack', 'Backend', 'Frontend', 'UI/UX Designer'];
        $keyUUIDDivision = [
            'c251492d-e2b7-4b69-a4da-f97c8469aea8',
            'c020cebe-a8a7-4ef2-8078-f973970cbde6',
            'db329fc3-e17b-4ce4-ae3b-e12ae1e360ee',
            '44fd6430-5eb7-4679-909e-47c2b7b67283',
            '4c1ea588-7916-4efc-a896-a33755dd08ea',
            '76276b40-2800-46e2-8184-7a24ead46381'
        ]; //hanya untuk dummy data!

        foreach ($divisions as $key => $division) {
            Division::create([
                'id' => $keyUUIDDivision[$key],
                'name' => $division,
            ]);
        }

        // factory data employees
        Employee::factory(10)->create();

        // factory dumy admin roles
        User::create([
            'name' => 'Admin Gede Hari Yoga Nanda',
            'username' => 'admin-2',
            'email' => 'admin@example.com',
            'password' => bcrypt('pastibisa'),
            'phone' => '083133737660',
            'roles' => 'admin',
        ]);

        User::create([
            'name' => 'Atasan Pak Bambang',
            'username' => 'bambang',
            'email' => 'bambang@example.com',
            'password' => bcrypt('pastibisa'),
            'phone' => '083133737661',
            'roles' => 'atasan',
        ]);
    }
}
