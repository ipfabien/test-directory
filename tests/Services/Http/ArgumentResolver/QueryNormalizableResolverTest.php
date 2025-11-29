<?php

declare(strict_types=1);

namespace App\Tests\Services\Http\ArgumentResolver;

use App\Controller\Api\Contact\GetContactListRequest;
use App\Controller\Api\Contact\GetContactRequest;
use App\Controller\Api\Manager\GetManagerRequest;
use App\Services\Http\ArgumentResolver\QueryNormalizableResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class QueryNormalizableResolverTest extends TestCase
{
    public function testSupportsReturnsTrueForGetWithNormalizableType(): void
    {
        $resolver = new QueryNormalizableResolver();
        $request  = Request::create('/api/contacts', Request::METHOD_GET);

        $argument = new ArgumentMetadata(
            'filters',
            GetContactListRequest::class,
            false,
            false,
            null
        );

        self::assertTrue($resolver->supports($request, $argument));
    }

    public function testSupportsReturnsFalseForNonReadMethods(): void
    {
        $resolver = new QueryNormalizableResolver();
        $request  = Request::create('/api/contacts', Request::METHOD_POST);

        $argument = new ArgumentMetadata(
            'filters',
            GetContactListRequest::class,
            false,
            false,
            null
        );

        self::assertFalse($resolver->supports($request, $argument));
    }

    public function testResolveMergesRouteAttributesAndQueryParametersForContactList(): void
    {
        $resolver = new QueryNormalizableResolver();

        $request = Request::create(
            '/api/contacts',
            Request::METHOD_GET,
            ['firstname' => 'John'], // attributes (e.g. from route)
            [],
            [],
            [],
            null
        );

        $request->query->set('lastname', 'Doe');
        $request->query->set('email', 'john.doe@example.com');
        $request->query->set('page', '2');
        $request->query->set('perPage', '20');

        $argument = new ArgumentMetadata(
            'filters',
            GetContactListRequest::class,
            false,
            false,
            null
        );

        $result = iterator_to_array($resolver->resolve($request, $argument));

        self::assertCount(1, $result);
        self::assertInstanceOf(GetContactListRequest::class, $result[0]);

        /** @var GetContactListRequest $dto */
        $dto = $result[0];

        self::assertSame('John', $dto->firstname());
        self::assertSame('Doe', $dto->lastname());
        self::assertSame('john.doe@example.com', $dto->email());
        self::assertNull($dto->phone());
        self::assertSame(2, $dto->page());
        self::assertSame(20, $dto->perPage());
    }

    public function testResolveBuildsGetContactRequestFromRouteAttributes(): void
    {
        $resolver = new QueryNormalizableResolver();

        $request = Request::create(
            '/api/contact/123',
            Request::METHOD_GET,
            ['id' => '123'],
            [],
            [],
            [],
            null
        );

        $argument = new ArgumentMetadata(
            'requestDto',
            GetContactRequest::class,
            false,
            false,
            null
        );

        $result = iterator_to_array($resolver->resolve($request, $argument));

        self::assertCount(1, $result);
        self::assertInstanceOf(GetContactRequest::class, $result[0]);

        /** @var GetContactRequest $dto */
        $dto = $result[0];

        self::assertSame('123', $dto->id());
    }

    public function testResolveBuildsGetManagerRequestFromRouteAttributes(): void
    {
        $resolver = new QueryNormalizableResolver();

        $request = Request::create(
            '/api/manager/abc',
            Request::METHOD_GET,
            ['id' => 'abc'],
            [],
            [],
            [],
            null
        );

        $argument = new ArgumentMetadata(
            'requestDto',
            GetManagerRequest::class,
            false,
            false,
            null
        );

        $result = iterator_to_array($resolver->resolve($request, $argument));

        self::assertCount(1, $result);
        self::assertInstanceOf(GetManagerRequest::class, $result[0]);

        /** @var GetManagerRequest $dto */
        $dto = $result[0];

        self::assertSame('abc', $dto->id());
    }
}


