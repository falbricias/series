<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private Generator $faker;
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //$this->addSeries();
        $this->addUsers(50);
    }

    public function addSeries(){
        for ($i = 0; $i < 50 ; $i++){

            $serie = new Serie();

            $serie
                ->setName(implode(' ',$this->faker->words(3)))
                ->setVote($this->faker->numberBetween(0, 10))
                ->setStatus($this->faker->randomElement(['Cancelled', 'Ended', 'Returning']))
                ->setPoster('poser.png')
                ->setTmdbId(123)
                ->setPopularity(250)
                ->setFirstAirDate($this->faker->dateTimeBetween('-6 month'))
                ->setLastAirDate($this->faker->dateTimeBetween($serie->getFirstAirDate()))
                ->setGenres($this->faker->randomElement(['Western', 'Comedy', 'Drama']))
                ->setBackdrop('backdrop.png');

            $this->entityManager->persist($serie);
        }
        $this->entityManager->flush();
    }

    private function addUsers(int $numberUsers){
        for ($i = 0 ; $i < $numberUsers ; $i++){
            $user = new User();

            $user
                ->setRoles(['ROLE_USER'])
                ->setEmail($this->faker->email)
                ->setFirstname($this->faker->firstName)
                ->setLastname($this->faker->lastName);

            $password = $this->passwordHasher->hashPassword($user, '123');
            $user->setPassword($password);

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }
}
