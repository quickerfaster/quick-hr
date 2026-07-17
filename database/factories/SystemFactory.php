<?php

namespace Database\Factories;

use App\Modules\System\Models\System;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemFactory extends Factory
{
    protected $model = System::class;

    public function definition(): array
    {
        return ['id' => 1];
    }
}
