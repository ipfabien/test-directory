<?php

declare(strict_types=1);

namespace App\Controller\Api\Contact;

use App\Domain\Contact\ContactRepository;
use App\Domain\Contact\CreateContact;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CreateContactController
{
    private ContactRepository $contactRepository;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(ContactRepository $contactRepository, UrlGeneratorInterface $urlGenerator)
    {
        $this->contactRepository = $contactRepository;
        $this->urlGenerator      = $urlGenerator;
    }

    public function __invoke(CreateContactRequest $request): JsonResponse
    {
        $externalId = $this->contactRepository->create(
            CreateContact::create(
                $request->firstname(),
                $request->lastname(),
                $request->email(),
                $request->managerId(),
                $request->phone(),
                $request->note()
            )
        );

        return new JsonResponse(
            [],
            Response::HTTP_CREATED,
            [
                'Location' => $this->urlGenerator->generate(
                    'api_get_contact',
                    ['id' => $externalId->toString()]
                )
            ]
        );
    }
}
