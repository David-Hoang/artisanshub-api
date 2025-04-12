<?php

namespace Database\Seeders;

use App\Models\CraftsmanJob;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CraftsmanJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $jobs = [
            'Plombier',
            'Électricien',
            'Maçon',
            'Peintre',
            'Menuisier',
            'Jardinier',
            'Couvreur',
            'Carreleur',
            'Chauffagiste',
            'Vitrier',
        ];

        foreach ($jobs as $job) {
            CraftsmanJob::create([
                'name' => $job,
            ]);
        }

    }
}
