<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i=1; $i <= 3; $i++) { 
            $category = new Category;
            $category->setTitle($faker->sentence(3, false));

            $manager->persist($category);

            // créer entre 4 et 6 articles
            for ($j=1; $j <= mt_rand(4,6); $j++) { 
                
                $article = new Article;
                
                $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
                // join() prend en param un séparateur et un tableu et ça renvoie une chaîne de caractère

                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween("-6 months"))
                        ->setCategory($category)
                ;

                $manager->persist($article);
            
                for ($k=1; $k <= mt_rand(5,10); $k++) { 
                    $comment = new Comment;

                    $content = '<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>';

                    $now = new \DateTime;
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;

                    $comment->setAuthor($faker->name())
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTime("-" . $days . " days"))
                            ->setArticle($article)
                    ;

                    $manager->persist($comment);


                }
            
            }

        }
        $manager->flush();
        //flush() permet d'exécuter la requête SQL préparée grâce à persist()
    }
}
