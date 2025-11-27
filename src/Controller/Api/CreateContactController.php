<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Domain\Contact\ContactRepository;
use App\Domain\Contact\CreateContact;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateContactController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function __invoke(CreateContactRequest $request): JsonResponse
    {
        $createContact = CreateContact::create(
            $request->firstname(),
            $request->lastname(),
            $request->email(),
            $request->phone()
        );

        $this->contactRepository->create($createContact);

        // For now we just return an empty JSON with HTTP 201.
        return new JsonResponse([], Response::HTTP_CREATED);
    }
}


