<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ){}
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin
        $admin = new User();
        $admin
            ->setEmail('admin@admin.fr')
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setWarningCount(0)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($admin);
        $manager->flush(); // Admin enregistré en base de données

        for ($i=0; $i < 100; $i++) {
            $article = new Article();
            $article
                ->setTitle($faker->sentence())
                ->setSlug($faker->slug())
                ->setKeywords($faker->words(5, true))
                ->setDescription($faker->sentence())
                ->setContent($faker->text())
                ->setIsPublished($faker->boolean(70))
                ->setIsArchived($faker->boolean(10))
                ->setAuthor($admin)
            ;

            $manager->persist($article);
            $this->addReference('ARTICLE_' . $i, $article);

            if($i % 100 === 0) {
                $manager->flush(); // Article enregistré en base de données tous les 10 articles
            }
        }


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}