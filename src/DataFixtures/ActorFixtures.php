<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use App\Entity\Actor;
use App\Entity\Program;
use App\Repository\ProgramRepository;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $programs = $manager->getRepository(Program::class)->findAll();

        for ($i = 0; $i < 10; $i++) {

            $actor = new Actor();
            $actor->setName($faker->firstName() . " " . $faker->lastName());
            for ($j = 0; $j < 4; $j++) {
                $actor->addProgram($faker->randomElement($programs));
            }
            $manager->persist($actor);

        }
        $manager->flush();
    }

    public function getDependencies()

    {
        return [
            ProgramFixtures::class,
        ];
    }
}
