<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(StaffSeeder::class);

        $fiction = Category::firstOrCreate(
            ['name' => 'Fiction'],
            ['description' => 'Fiction and novels'],
        );
        $science = Category::firstOrCreate(
            ['name' => 'Science'],
            ['description' => 'Science and technology'],
        );

        Book::firstOrCreate(
            ['ISBN' => '9780000000001'],
            ['name' => 'Example Novel', 'author' => 'Sample Author', 'category_id' => $fiction->id],
        );
        Book::firstOrCreate(
            ['ISBN' => '9780000000002'],
            ['name' => 'Example Science', 'author' => 'Sample Author', 'category_id' => $science->id],
        );
    }
}
