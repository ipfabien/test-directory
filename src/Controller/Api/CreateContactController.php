<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateContactController
{
    public function __invoke(CreateContactRequest $request): JsonResponse
    {
        // For now we just return an empty JSON with HTTP 201.
        return new JsonResponse([], Response::HTTP_CREATED);
    }
}


