<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Domain\Shared\ExternalId;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

final class ContactApiTest extends AbstractApiTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testCreateContactAndGetItWithManager(): void
    {
        $email            = 'john.doe.' . uniqid('', true) . '@example.com';
        $managerExternalId = '11111111-1111-1111-1111-111111111111';

        $this->client->request(
            'POST',
            '/api/contact',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstname' => 'John',
                'lastname'  => 'Doe',
                'email'     => $email,
                'phone'     => '+33123456789',
                'note'      => 'Test contact',
                'managerId' => $managerExternalId,
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $location = $this->client->getResponse()->headers->get('Location');
        self::assertNotNull($location);

        // Extract externalId from Location header (/api/contact/{id})
        $parts      = explode('/', trim($location, '/'));
        $externalId = end($parts);
        ExternalId::fromString((string) $externalId); // validate format

        $this->client->request('GET', sprintf('/api/contact/%s', $externalId));
        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame($email, $data['email']);
        self::assertArrayHasKey('manager', $data);
        self::assertNotNull($data['manager']);
        self::assertSame($managerExternalId, $data['manager']['externalId']);
    }

    public function testCreateContactValidationError(): void
    {
        $this->client->request(
            'POST',
            '/api/contact',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstname' => '',
                'lastname'  => 'Doe',
                'email'     => 'not-an-email',
                'managerId' => '11111111-1111-1111-1111-111111111111',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testGetContactNotFound(): void
    {
        $this->client->request(
            'GET',
            '/api/contact/99999999-9999-9999-9999-999999999999'
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testGetContactListWithFiltersAndPagination(): void
    {
        $managerExternalId = '11111111-1111-1111-1111-111111111111';

        // Create two contacts
        foreach (['AliceSearch', 'BobSearch'] as $firstname) {
            $email = strtolower($firstname) . '.' . uniqid('', true) . '@example.com';

            $this->client->request(
                'POST',
                '/api/contact',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode([
                    'firstname' => $firstname,
                    'lastname'  => 'Test',
                    'email'     => $email,
                    'managerId' => $managerExternalId,
                ], JSON_THROW_ON_ERROR)
            );

            self::assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        }

        $this->client->request(
            'GET',
            '/api/contacts?firstname=AliceSearch&page=1&perPage=10'
        );

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('result', $payload);
        self::assertArrayHasKey('count', $payload);
        self::assertArrayHasKey('pagination', $payload);
        self::assertGreaterThanOrEqual(1, (int) $payload['count']);
    }

    public function testGetContactManager(): void
    {
        $managerExternalId = '11111111-1111-1111-1111-111111111111';
        $email             = 'manager.for.contact.api.' . uniqid('', true) . '@example.com';

        $this->client->request(
            'POST',
            '/api/contact',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstname' => 'Mgr',
                'lastname'  => 'Link',
                'email'     => $email,
                'managerId' => $managerExternalId,
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $location = $this->client->getResponse()->headers->get('Location');
        self::assertNotNull($location);
        $parts      = explode('/', trim($location, '/'));
        $externalId = end($parts);

        $this->client->request('GET', sprintf('/api/contact/%s/manager', $externalId));

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame($managerExternalId, $data['externalId']);
    }
}


