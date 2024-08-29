<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $i = 1;
        while ($i <= 4) {
            DB::table('posts')->insert([
                'title' => $faker->paragraph(),
                'user_id' => 4,
                'desc' => $faker->paragraph(3),
                'post_desc' => $faker->paragraph(500)
            ]);
            $i++;
        }
    }
}
