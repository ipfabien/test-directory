<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

final class ManagerApiTest extends AbstractApiTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testGetManagerList(): void
    {
        $this->client->request('GET', '/api/managers');

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('result', $data);
        self::assertArrayHasKey('count', $data);
        self::assertGreaterThanOrEqual(1, (int) $data['count']);
    }

    public function testGetManagerWithContacts(): void
    {
        // We rely on seeded managers; at least one should exist with this externalId.
        $managerId = '11111111-1111-1111-1111-111111111111';

        $this->client->request('GET', sprintf('/api/manager/%s', $managerId));

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('manager', $data);
        self::assertSame($managerId, $data['manager']['externalId']);
        self::assertArrayHasKey('contacts', $data);
        self::assertArrayHasKey('result', $data['contacts']);
        self::assertArrayHasKey('count', $data['contacts']);
    }

    public function testGetManagerNotFound(): void
    {
        $this->client->request('GET', '/api/manager/99999999-9999-9999-9999-999999999999');

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}


