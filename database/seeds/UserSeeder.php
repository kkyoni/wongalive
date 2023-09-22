<?php

use Illuminate\Database\Seeder;
use App\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
{
        User::create([
            'username' => 'ais_mico'
            'fname' => 'AIS',
            'lname' => 'Technolabs',
            'number' => '9898980001',
            'email' => 'aistechnolabs@mailinator.com',
            'password' => bcrypt('ais@1234'),
            'type' => 'superadmin',
        	'status' => 'active',
            ]);
    }
}