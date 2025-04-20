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
            [
                'name' => 'Plombier',
                'img_title' => 'Installation et dépannage plomberie',
                'img_path' => null,
                'description' => 'Le plombier installe, entretient et répare les systèmes de plomberie, de chauffage et de distribution d’eau dans les bâtiments.',
            ],
            [
                'name' => 'Électricien',
                'img_title' => 'Travaux électriques sécurisés',
                'img_path' => null,
                'description' => 'L’électricien réalise l’installation, la maintenance et la mise aux normes des systèmes électriques dans tous types de bâtiments.',
            ],
            [
                'name' => 'Maçon',
                'img_title' => 'Construction et gros œuvre',
                'img_path' => null,
                'description' => 'Le maçon construit les fondations, les murs et les cloisons des bâtiments en utilisant divers matériaux comme la brique ou le béton.',
            ],
            [
                'name' => 'Peintre',
                'img_title' => 'Finitions soignées et décoration',
                'img_path' => null,
                'description' => 'Le peintre en bâtiment prépare les surfaces et applique peintures ou revêtements pour protéger et décorer les murs intérieurs et extérieurs.',
            ],
            [
                'name' => 'Menuisier',
                'img_title' => 'Travaux de menuiserie sur mesure',
                'img_path' => null,
                'description' => 'Le menuisier conçoit, fabrique et installe des ouvrages en bois comme les portes, fenêtres, escaliers ou meubles sur mesure.',
            ],
            [
                'name' => 'Jardinier',
                'img_title' => 'Entretien de jardins et espaces verts',
                'img_path' => null,
                'description' => 'Le jardinier entretient les espaces verts, plante, taille, tond et veille à la bonne santé des végétaux dans les jardins privés ou publics.',
            ],
            [
                'name' => 'Couvreur',
                'img_title' => 'Pose et rénovation de toitures',
                'img_path' => null,
                'description' => 'Le couvreur installe, répare et entretient les toitures pour assurer l’étanchéité et la protection des bâtiments contre les intempéries.',
            ],
            [
                'name' => 'Carreleur',
                'img_title' => 'Pose de carrelage soignée',
                'img_path' => null,
                'description' => 'Le carreleur pose le carrelage sur les sols et murs pour un rendu esthétique, durable et facile à entretenir.',
            ],
            [
                'name' => 'Chauffagiste',
                'img_title' => 'Confort thermique assuré',
                'img_path' => null,
                'description' => 'Le chauffagiste installe et entretient les systèmes de chauffage, de ventilation et parfois de climatisation dans les bâtiments.',
            ],
            [
                'name' => 'Vitrier',
                'img_title' => 'Remplacement et pose de vitres',
                'img_path' => null,
                'description' => 'Le vitrier découpe, installe et remplace les vitrages sur fenêtres, portes, vitrines ou cloisons en verre.',
            ],
        ];
    
        foreach ($jobs as $job) {
            CraftsmanJob::create($job);
        }
    }
}
