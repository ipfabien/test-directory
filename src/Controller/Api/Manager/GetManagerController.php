<?php

declare(strict_types=1);

namespace App\Controller\Api\Manager;

use App\Domain\Contact\ContactRepository;
use App\Domain\Manager\ManagerRepository;
use App\Domain\Shared\ExternalId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/manager/{id}", name="api_get_manager", methods={"GET"})
 */
final class GetManagerController
{
    private ManagerRepository $managerRepository;

    private ContactRepository $contactRepository;

    public function __construct(ManagerRepository $managerRepository, ContactRepository $contactRepository)
    {
        $this->managerRepository = $managerRepository;
        $this->contactRepository = $contactRepository;
    }

    public function __invoke(GetManagerRequest $request): JsonResponse
    {
        $externalId = ExternalId::fromString($request->id());

        $manager  = $this->managerRepository->find($externalId);
        $contacts = $this->contactRepository->findByManager($externalId);

        return new JsonResponse(
            [
                'manager'  => $manager->normalize(),
                'contacts' => [
                    'result' => $contacts->normalize(),
                    'count'  => $contacts->count(),
                ],
            ],
            Response::HTTP_OK
        );
    }
}
