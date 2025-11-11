<?php

namespace Database\Seeders;

use App\Models\Mesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mesas = [
            ['identificador' => 'Mesa 01', 'lugares' => 4],
            ['identificador' => 'Mesa 02', 'lugares' => 2],
            ['identificador' => 'Mesa 03', 'lugares' => 6],
            ['identificador' => 'Mesa 04', 'lugares' => 4],
            ['identificador' => 'Mesa 05', 'lugares' => 8],
            ['identificador' => 'Mesa 06', 'lugares' => 2],
            ['identificador' => 'Mesa 07', 'lugares' => 4],
            ['identificador' => 'Mesa 08', 'lugares' => 6]
        ];

        foreach ($mesas as $mesa) {
            Mesa::create($mesa);
        }
    }
}
