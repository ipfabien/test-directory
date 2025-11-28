<?php

declare(strict_types=1);

namespace App\Controller\Api\Contact;

use App\Domain\Contact\ContactRepository;
use App\Domain\Shared\ExternalId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GetContactManagerController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function __invoke(GetContactRequest $request): JsonResponse
    {
        $manager = $this->contactRepository->findManagerForContact(
            ExternalId::fromString($request->id())
        );

        return new JsonResponse($manager->normalize(), Response::HTTP_OK);
    }
}
