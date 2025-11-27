<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Domain\Contact\ContactRepository;
use App\Domain\Contact\Exception\ContactNotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GetContactController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function __invoke(GetContactRequest $request): JsonResponse
    {
        try {
            $contact = $this->contactRepository->find($request->id());
        } catch (ContactNotFound $exception) {
            return new JsonResponse(['error' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($contact->normalize(), Response::HTTP_OK);
    }
}


