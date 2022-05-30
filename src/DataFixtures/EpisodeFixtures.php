<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Episode;
use Faker\Factory;
use App\Service\Slugify;



class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i < 80; $i++) {

            $episode = new Episode();
            $episode->setTitle($faker->words(2, true));
            $episode->setSlug($this->slugify->generate($episode->getTitle()));
            $episode->setNumber($faker->numberBetween(1, 12));
            $episode->setSynopsis($faker->paragraphs(3, true));
            $episode->setSeason($this->getReference('season_' . $faker->numberBetween(1, 24)));
            $manager->persist($episode);
        }
        $manager->flush();
    }

    public function getDependencies()

    {
        return [
            SeasonFixtures::class,
        ];
    }
}
