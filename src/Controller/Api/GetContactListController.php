<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Domain\Contact\ContactRepository;
use App\Domain\Contact\SearchFilter;
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
        $contacts = $this->contactRepository->search(
            SearchFilter::create(
                $request->firstname(),
                $request->lastname(),
                $request->email(),
                $request->phone()
            )
        );

        return new JsonResponse($contacts->normalize(), Response::HTTP_OK);
    }
}


