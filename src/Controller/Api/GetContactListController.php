<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Domain\Contact\ContactRepository;
use App\Domain\Contact\SearchFilter;
use App\Domain\Shared\Pagination;
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
        $filter = SearchFilter::create(
            $request->firstname(),
            $request->lastname(),
            $request->email(),
            $request->phone()
        );

        $pagination = Pagination::create($request->page(), $request->perPage());

        $contacts = $this->contactRepository->search($filter, $pagination);

        return new JsonResponse(
            [
                'result' => $contacts->normalize(),
                'count' => $contacts->count(),
                'pagination' => [
                    'page' => $pagination->page(),
                    'perPage' => $pagination->perPage(),
                ],
            ],
            Response::HTTP_OK
        );
    }
}


