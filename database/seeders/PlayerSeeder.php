<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('players')->insert([
            'name' => 'Sam',
            'game_id' => 1,
            'code' => '1111',
            'order' => 1,
        ]);

        DB::table('players')->insert([
            'name' => 'Eva',
            'game_id' => 1,
            'code' => '2222',
            'order' => 2
        ]);
    }
}
