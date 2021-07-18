<?php

namespace App\Tests\Helper;

use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Assert;

/**
 * Trait ApiTrait
 * @package App\Tests\Helper
 */
trait ApiTrait
{
    /**
     * @param string $token
     * @param string $project
     * @return array
     */
    protected function getHeaders(string $token = '', string $project = 'bbs')
    {
        return array_merge(
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Project' => $project,
            ],
            ($token ? ['HTTP_AUTHORIZATION' => 'Bearer ' . $token] : [])
        );
    }

    /**
     * @param Client $client
     * @return bool
     */
    protected function checkResponseSuccess(Client $client)
    {
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @param Client $client
     * @return array
     */
    protected function getContent(Client $client): array
    {
        return json_decode($client->getResponse()->getContent(), true);
    }

    /**
     * @param Client $client
     * @param int $statusCode
     */
    protected function checkJsonResponse(Client $client, $statusCode = Response::HTTP_OK)
    {
        $response = $client->getResponse();
        Assert::assertEquals($statusCode, $response->getStatusCode());
        Assert::assertTrue($response->headers->contains('Content-Type', 'application/json'));
        Assert::assertJson($response->getContent());
    }

    /**
     * @param int $id
     * @param bool $isAdmin
     * @return string
     */
    public function getToken(
        int $id = 1,
        bool $isAdmin = true
    ): string {
        return $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create(
            $this->generateUser($id, $isAdmin)
        );
    }

    /**
     * @param int $id
     * @param bool $isAdmin
     * @return User
     */
    public function generateUser(int $id = 1, bool $isAdmin = true)
    {
        return new User($id, array_merge(['ROLE_USER'], $isAdmin ? ['ROLE_ADMIN'] : []));
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $contentArray
     * @param string $project
     * @param int $userRef
     * @return Client
     */
    public function createRequestWithToken(
        string $method,
        string $uri,
        array $parameters = [],
        array $contentArray = [],
        string $project = 'bbs',
        int $userRef = 1
    ) {
        /** @var Client $client */
        $client = $this->getContainer()->get('test.client');

        $client->request(
            $method,
            $uri,
            $parameters,
            [],
            $this->getHeaders($this->getToken($userRef), $project),
            $contentArray ? json_encode($contentArray) : null
        );

        return $client;
    }


    /**
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $contentArray
     * @param string $project
     * @return Client
     */
    public function createRequestWithoutToken(
        string $method,
        string $uri,
        array $parameters = [],
        array $contentArray = [],
        string $project = 'bbs'
    ) {
        /** @var Client $client */
        $client = $this->getContainer()->get('test.client');

        $client->request(
            $method,
            $uri,
            $parameters,
            [],
            $this->getHeaders('', $project),
            $contentArray ? json_encode($contentArray) : null
        );

        return $client;
    }
}
