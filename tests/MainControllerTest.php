<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', "Serie's detail");
    }

    public function testCreatedSerieIsWorkingIfNotLogged(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/serie/add');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testCreatedSerieIsWorkingIfLogged(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/serie/add');

        // Récupération du repository
        $userRepository = static::getContainer()->get(UserRepository::class);
        // Récupération d'un user
        $user = $userRepository->findOneBy(['email' => 'francois@mail.com']);
        //Simule une connexion avec un user
        $client->loginUser($user);

        $crawler = $client->request('GET', '/serie/add');

        $this->assertResponseIsSuccessful();
    }
}
