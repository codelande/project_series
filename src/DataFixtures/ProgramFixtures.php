<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)

    {
        $faker = Factory::create();

        for ($i = 1; $i < 6; $i++) {

            $program = new Program();
            $program->setTitle($faker->words(2, true));
            $program->setSynopsis($faker->paragraphs(3, true));
            $program->setCategory($this->getReference('category_' . CategoryFixtures::CATEGORIES[rand(0, 5)]));
            $this->addReference('program_' . $i, $program);
            $manager->persist($program);
        }
        $manager->flush();
    }

    public function getDependencies()

    {
        return [
            CategoryFixtures::class,
        ];
    }
}
