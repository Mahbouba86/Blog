<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\DataFixtures\ArticleFixtures;
use App\Entity\Block;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class BlockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupération des articles nouvellement créés
        $articles = [];
        for ($i=0; $i < 1000; $i++) {
            $articles[] = $this->getReference('ARTICLE_' . $i, Article::class);
        }

        $j = 0;
        foreach($articles as $item) { // Pour chaque article du tableau
            $count = $faker->numberBetween(1, 2); // Choisir en 1 et 2

            for ($i=0; $i < $count; $i++) { // Pour le nombre choisi

                $block = new Block(); // Instanciation d'un objet Block
                $block
                    ->setName($faker->word(2)) // Ajouter un nom au bloc
                    ->setContent($faker->sentence()) // Ajouter du contenu au bloc
                    ->addArticle($item) // Assignation de l'article au bloc
                ;

                $manager->persist($block); // Création de l'enregistrement en BDD
            }

            if($j % 100 === 0) { // Si $j est un multiple de 100
                $manager->flush(); // Enregistrement des blocs en BDD
            }

            $j++; // Incrémentation de $j
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ArticleFixtures::class]; // Dépendances des articles pour BlocFixtures
    }
}