<?php

declare(strict_types=1);

namespace App\Controller\Api\Manager;

use App\Domain\Manager\ManagerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GetManagerListController
{
    private ManagerRepository $managerRepository;

    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function __invoke(): JsonResponse
    {
        $managers = $this->managerRepository->findAll();

        return new JsonResponse(
            [
                'result' => $managers->normalize(),
                'count'  => \count($managers),
            ],
            Response::HTTP_OK
        );
    }
}
