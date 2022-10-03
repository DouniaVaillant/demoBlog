<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Voiture;

class VoituresFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i <=15; $i++)
        {
            $prix = rand(1000, 10000);
            $voiture = new Voiture;
            $voiture->setMarque("Marque de la voiture n°$i")
                    ->setModele("Modèle de la voiture n°$i")
                    ->setPrix($i * 3.7)
                    ->setDescription("Description de la voiture n°$i") ;
                     
            $manager->persist($voiture);
            
        }
        $manager->flush();
    
    }
}
