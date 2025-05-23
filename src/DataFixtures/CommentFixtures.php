<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Supposons que j'ai 1000 articles et 10 users en référence
        for ($i = 0; $i < 100; $i++) {
            $article = $this->getReference('ARTICLE_' . $i, Article::class);

            for ($j = 0; $j < 5; $j++) {
                $comment = new Comment();

                $userIndex = $faker->numberBetween(0, 9);
                $author = $this->getReference('USER_' . $userIndex, User::class);

                $comment
                    ->setAuthor($author)
                    ->setContent($faker->sentence())
                    ->setArticle($article);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ArticleFixtures::class,
            UserFixtures::class,
        ];
    }
}
