<?php

namespace App\DataFixtures;

use App\Entity\Voiture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VoitureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $voiture = new Voiture;

            $voiture->setMarque("Marque de la voiture n°$i")
                ->setModele("Modèle de la voiture n°$i")
                ->setPrix($i * 3.7)
                ->setDescription("Description de la voiture n°$i");
            $manager->persist($voiture);
        }
        $manager->flush();
    }
}
