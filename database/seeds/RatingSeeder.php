<?php

use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tblratings')->insert([
            'rate_description' => 'Sad',
        ]); 

        DB::table('tblratings')->insert([
            'rate_description' => 'Neutral',
        ]);

        DB::table('tblratings')->insert([
            'rate_description' => 'Happy',
        ]);
    }
}
