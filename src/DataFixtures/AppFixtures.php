<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->addSeries($manager);
    }

    public function addSeries(ObjectManager $manager){
        for ($i = 0; $i < 50 ; $i++){

            $serie = new Serie();

            $serie
                ->setName("Serie $i")
                ->setVote(8)
                ->setStatus('Ended')
                ->setPoster('poser.png')
                ->setTmdbId(123)
                ->setPopularity(250)
                ->setFirstAirDate(new \DateTime('-6 month'))
                ->setLastAirDate(new \DateTime('-1 month'))
                ->setGenres('Western')
                ->setBackdrop('backdrop.png');

            $manager->persist($serie);
        }
        $manager->flush();
    }

}
