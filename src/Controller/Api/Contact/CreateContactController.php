<?php

declare(strict_types=1);

namespace App\Controller\Api\Contact;

use App\Domain\Contact\ContactRepository;
use App\Domain\Contact\CreateContact;
use App\Message\Event\Contact\ContactCreated;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api/contact", name="api_create_contact", methods={"POST"})
 */
final class CreateContactController
{
    private ContactRepository $contactRepository;

    private UrlGeneratorInterface $urlGenerator;

    private MessageBusInterface $eventBus;

    public function __construct(
        ContactRepository $contactRepository,
        UrlGeneratorInterface $urlGenerator,
        MessageBusInterface $eventBus
    ) {
        $this->contactRepository = $contactRepository;
        $this->urlGenerator      = $urlGenerator;
        $this->eventBus          = $eventBus;
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

        $this->eventBus->dispatch(
            ContactCreated::create(
                $externalId->toString(),
                $request->firstname(),
                $request->lastname(),
                $request->email()
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
