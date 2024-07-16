<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignSeeder extends Seeder
{
    public function run()
    {
        $campaigns = [
            [
                'title' => 'Campaign 1',
                'date_start' => Carbon::create('2023', '01', '01'),
                'date_end' => Carbon::create('2023', '01', '31'),
                'max_selections' => 3,
                'launched' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Campaign 2',
                'date_start' => Carbon::create('2023', '02', '01'),
                'date_end' => Carbon::create('2023', '02', '28'),
                'max_selections' => 2,
                'launched' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Campaign 3',
                'date_start' => Carbon::create('2023', '03', '01'),
                'date_end' => Carbon::create('2023', '03', '31'),
                'max_selections' => 4,
                'launched' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Campaign 4',
                'date_start' => Carbon::create('2023', '04', '01'),
                'date_end' => Carbon::create('2023', '04', '30'),
                'max_selections' => 1,
                'launched' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('campaigns')->insert($campaigns);
    }
}
