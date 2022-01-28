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
        DB::table('users')->delete();
        User::create(array(
            'name'     => 'Gerard Balde',
            // 'username' => 'princegideon',
            'email'    => 'gerardbalde15@gmail.com',
            'password' => Hash::make('nrcp123123'),
            'user_id' => '1',
            'role' => '1',
            'status' => '1',
        ));
    }
}
