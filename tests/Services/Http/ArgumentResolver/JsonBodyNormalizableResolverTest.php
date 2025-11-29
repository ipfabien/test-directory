<?php

declare(strict_types=1);

namespace App\Tests\Services\Http\ArgumentResolver;

use App\Controller\Api\Contact\CreateContactRequest;
use App\Services\Http\ArgumentResolver\JsonBodyNormalizableResolver;
use App\Shared\Exception\BadRequestException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class JsonBodyNormalizableResolverTest extends TestCase
{
    public function testSupportsReturnsTrueForJsonNormalizableOnWriteMethods(): void
    {
        $resolver = new JsonBodyNormalizableResolver();
        $request  = Request::create(
            '/api/contact',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $argument = new ArgumentMetadata(
            'contact',
            CreateContactRequest::class,
            false,
            false,
            null
        );

        self::assertTrue($resolver->supports($request, $argument));
    }

    public function testSupportsReturnsFalseForNonWriteMethods(): void
    {
        $resolver = new JsonBodyNormalizableResolver();
        $request  = Request::create(
            '/api/contact',
            Request::METHOD_GET,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $argument = new ArgumentMetadata(
            'contact',
            CreateContactRequest::class,
            false,
            false,
            null
        );

        self::assertFalse($resolver->supports($request, $argument));
    }

    public function testSupportsReturnsFalseForNonJsonContentType(): void
    {
        $resolver = new JsonBodyNormalizableResolver();
        $request  = Request::create(
            '/api/contact',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'text/plain']
        );

        $argument = new ArgumentMetadata(
            'contact',
            CreateContactRequest::class,
            false,
            false,
            null
        );

        self::assertFalse($resolver->supports($request, $argument));
    }

    public function testResolveReturnsDenormalizedObjectFromValidJson(): void
    {
        $resolver = new JsonBodyNormalizableResolver();

        $payload = [
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'email'     => 'john.doe@example.com',
            'managerId' => '11111111-1111-1111-1111-111111111111',
            'phone'     => '+33123456789',
            'note'      => 'Test',
        ];

        $request = Request::create(
            '/api/contact',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR)
        );

        $argument = new ArgumentMetadata(
            'contact',
            CreateContactRequest::class,
            false,
            false,
            null
        );

        $result = iterator_to_array($resolver->resolve($request, $argument));

        self::assertCount(1, $result);
        self::assertInstanceOf(CreateContactRequest::class, $result[0]);

        /** @var CreateContactRequest $dto */
        $dto = $result[0];

        self::assertSame('John', $dto->firstname());
        self::assertSame('Doe', $dto->lastname());
        self::assertSame('john.doe@example.com', $dto->email());
        self::assertSame('+33123456789', $dto->phone());
        self::assertSame('Test', $dto->note());
        self::assertSame('11111111-1111-1111-1111-111111111111', $dto->managerId());
    }

    public function testResolveUsesEmptyArrayWhenBodyIsEmpty(): void
    {
        $resolver = new JsonBodyNormalizableResolver();

        $request = Request::create(
            '/api/contact',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $argument = new ArgumentMetadata(
            'contact',
            CreateContactRequest::class,
            false,
            false,
            null
        );

        $result = iterator_to_array($resolver->resolve($request, $argument));

        self::assertCount(1, $result);
        self::assertInstanceOf(CreateContactRequest::class, $result[0]);
    }

    public function testResolveThrowsBadRequestExceptionOnInvalidJson(): void
    {
        $resolver = new JsonBodyNormalizableResolver();

        $request = Request::create(
            '/api/contact',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{invalid json}'
        );

        $argument = new ArgumentMetadata(
            'contact',
            CreateContactRequest::class,
            false,
            false,
            null
        );

        $this->expectException(BadRequestException::class);

        iterator_to_array($resolver->resolve($request, $argument));
    }

    public function testResolveThrowsBadRequestExceptionWhenJsonIsNotArray(): void
    {
        $resolver = new JsonBodyNormalizableResolver();

        $request = Request::create(
            '/api/contact',
            Request::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '123'
        );

        $argument = new ArgumentMetadata(
            'contact',
            CreateContactRequest::class,
            false,
            false,
            null
        );

        $this->expectException(BadRequestException::class);

        iterator_to_array($resolver->resolve($request, $argument));
    }
}


