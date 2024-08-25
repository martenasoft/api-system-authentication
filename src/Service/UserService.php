<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Interfaces\UserServiceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface $client,
        private RequestStack $requestStack,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        $this->client->withOptions([
            'proxy' => 'http://127.0.0.1:7080'
        ]);
    }
    public function getUserId(string $email, string $password): int
    {
        $userApiUri = $this->parameterBag->get('api-params')['user-api']['uri'];
        $response = $this->client->request('POST', $userApiUri .'/find-by-login-password', [
            'json' => [
                'email' => $email,
                'password' => $password
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],

        ]);

        dump($response);
        $id = $response->toArray()['id'] ?? null;

        if (!$id) {
            throw new NotFoundHttpException('User not found');
        }

        return (int)$id;
    }

    public function getUser(): User
    {
        $requestData = $this->requestStack->getCurrentRequest()->toArray();
        $userId = $this->getUserId($requestData['email'], $requestData['password']);
        $user = new User();
        $user
            ->setId($userId)
            ->setEmail($requestData['email'])
            ->setPlainPassword($requestData['password'])
        ;

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        );
        $user->setPassword($hashedPassword);
        return $user;
    }
}
