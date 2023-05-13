<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker\Factory::create();
        \App\Models\User::create([
            'email' 		=> $faker->email,
            'name'	        => 'test',
            'password' 		=> '1q2w3e4r'
        ]);
    }
}
