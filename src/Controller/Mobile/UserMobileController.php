<?php
namespace App\Controller\Mobile;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mobile/user")
 */
class UserMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        if ($users) {
            return new JsonResponse($users, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $user = new User();

        
        

        $user->constructor(
            $request->get("email")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse($user, 200);

        
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find((int)$request->get("id"));

        if (!$user) {
            return new JsonResponse(null, 404);
        }

        
        

        $user->constructor(
            $request->get("email")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse($user, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find((int)$request->get("id"));

        if (!$user) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
}
