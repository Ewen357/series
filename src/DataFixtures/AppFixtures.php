<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');


        $this->addSeries($manager, $faker);
    }

    public function addSeries(ObjectManager $manager, Generator $generator){
        for ($i=0;$i<50;$i++){
           $serie=new Serie();

           $serie
               ->setName(implode(" ", $generator->words(3)))
               ->setVote($generator->numberBetween(0,10))
               ->setStatus($generator->randomElement(['ended','returning','canceled']))
               ->setPoster("poster.png")
               ->setTmdbId(123)
               ->setPopularity(250)
               ->setFirstAirDate($generator->dateTimeBetween('-6month'))
               ->setLastAirDate($generator->dateTimeBetween($serie->getFirstAirDate()))
               ->setGenres($generator->randomElement(['western','Comedy','Drama']))
               ->setBackdrop("Backdrop.png");
           $manager ->persist($serie);
        }
        $manager ->flush();

    }
}
