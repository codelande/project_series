<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Episode;
use Faker\Factory;


class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i < 80; $i++) {

            $episode = new Episode();
            $episode->setTitle($faker->words(2, true));
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
