<?php

namespace App\DataFixtures;

use App\Entity\Type;
use App\Entity\Voiture;
use EntityManager\Type\persist;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;


class VoitureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr_FR');
        for($i = 1; $i <=3; $i++)
            {
                $type = new Type;
                $type->setTitre($faker->sentence(3, false))
                     ->setNbRoues(rand(1,4));

                $manager->persist($type);
            for($j = 1; $j <= 5; $j++)
            {   $k= $i+$j;
                $prix = rand(1000, 100000) ;
                $voiture = new Voiture;
                $voiture->setMarque("marque n°$k")
                        ->setModele("modèle n°$k")
                        ->setPrix($prix)
                        ->setDescription("description de ma voiture ta vue tu connais n°$k")
                        ->setType($type);
                $manager->persist($voiture);
            }
        } 
        $manager->flush();
    }
}
