<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\System\Models\System;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        System::firstOrCreate(['id' => 1]);
    }
}
