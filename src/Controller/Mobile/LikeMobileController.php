<?php
namespace App\Controller\Mobile;

use App\Entity\Likez;
use App\Repository\LikezRepository;
use App\Repository\UserRepository;
use App\Repository\PosteRepository;
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
 * @Route("/mobile/like")
 */
class LikeMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(LikezRepository $likeRepository): Response
    {
        $likes = $likeRepository->findAll();

        if ($likes) {
            return new JsonResponse($likes, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, UserRepository $userRepository, PosteRepository $posteRepository): JsonResponse
    {
        $like = new Likez();

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
            $poste = $posteRepository->find((int)$request->get("poste"));
        if (!$poste) {
            return new JsonResponse("poste with id " . (int)$request->get("poste") . " does not exist", 203);
        }
        
        

        $like->constructor(
            $user,
            $poste
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($like);
        $entityManager->flush();

        return new JsonResponse($like, 200);

        
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, LikezRepository $likeRepository, UserRepository $userRepository, PosteRepository $posteRepository): Response
    {
        $like = $likeRepository->find((int)$request->get("id"));

        if (!$like) {
            return new JsonResponse(null, 404);
        }

        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
        return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
            $poste = $posteRepository->find((int)$request->get("poste"));
        if (!$poste) {
        return new JsonResponse("poste with id " . (int)$request->get("poste") . " does not exist", 203);
        }
        
        

        $like->constructor(
            $user,
            $poste
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($like);
        $entityManager->flush();

        return new JsonResponse($like, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, LikezRepository $likeRepository): JsonResponse
    {
        $like = $likeRepository->find((int)$request->get("id"));

        if (!$like) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($like);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
}
