<?php

namespace Database\Seeders;

use App\Models\ShfiRoot;
use Illuminate\Database\Seeder;

class ShfiRootSeeder extends Seeder
{
    public function run(): void
    {
        ShfiRoot::firstOrCreate(
            ['slug' => 'default'],
            ['name' => 'Slaughterhouse']
        );
    }
}
