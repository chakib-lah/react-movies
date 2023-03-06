<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Vérification si username ou email existe déjà dans la base de donnée
 *
 * @Route("/api/check-unique-user", name="api_users_check_unique", methods={Request::METHOD_GET})
 *
 */
class CheckUniqueController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function __invoke(Request $request, UserRepository $userRepository): JsonResponse
    {
        $slug = $request->get('slug');
        $criteria = Criteria::create()
            ->orWhere(Criteria::expr()->eq('username',$slug))
            ->orWhere(Criteria::expr()->eq('email',$slug));

        $find = (bool)$userRepository->matching($criteria)->count();


        return new JsonResponse($find, Response::HTTP_OK);
    }

}