<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Thanh Huynh',
            'username' => 'supersu',
            'email' => 'thanhmail@email.com',
            'password' => bcrypt('awsome'),
        ]);
    }
}
