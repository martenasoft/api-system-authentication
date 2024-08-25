<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Service\Interfaces\UserServiceInterface;
use App\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthTest extends ApiTestCase
{
    public function testAuth(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set(UserService::class, $this->getMockUserService($container));
        $result = $client->request('POST', 'https://authentication.localhost/login', [
            'json' => [
                'email' => 'test@user.com',
                'password' => '123123'
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertNotEmpty($result->toArray()['token']);
    }

    private function getMockUserService(ContainerInterface $container): UserServiceInterface
    {
        $parameterBag = $container->get(ParameterBagInterface::class);
        $client = $container->get(HttpClientInterface::class);
        $requestStack = $container->get(RequestStack::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $userServiceMocked = new class (
            $parameterBag,
            $client,
            $requestStack,
            $passwordHasher
        ) extends UserService {
            public function getUserId(string $email, string $password): int
            {
                return 1;
            }
        };

        return $userServiceMocked;
    }
}
