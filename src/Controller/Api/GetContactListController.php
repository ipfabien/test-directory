<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Domain\Contact\ContactRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GetContactListController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function __invoke(GetContactListRequest $request): JsonResponse
    {
        $contacts = $this->contactRepository->search();

        return new JsonResponse($contacts->normalize(), Response::HTTP_OK);
    }
}


