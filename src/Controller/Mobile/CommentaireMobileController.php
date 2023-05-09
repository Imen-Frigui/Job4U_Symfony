<?php
namespace App\Controller\Mobile;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use App\Repository\PosteRepository;
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
 * @Route("/mobile/commentaire")
 */
class CommentaireMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAll();

        if ($commentaires) {
            return new JsonResponse($commentaires, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, PosteRepository $posteRepository, UserRepository $userRepository): JsonResponse
    {
        $commentaire = new Commentaire();

        
            $poste = $posteRepository->find((int)$request->get("poste"));
        if (!$poste) {
            return new JsonResponse("poste with id " . (int)$request->get("poste") . " does not exist", 203);
        }
        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
        

        $commentaire->constructor(
            $poste,
            $user,
            $request->get("description"),
            DateTime::createFromFormat("d-m-Y", $request->get("date"))
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return new JsonResponse($commentaire, 200);

        
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, CommentaireRepository $commentaireRepository, PosteRepository $posteRepository, UserRepository $userRepository): Response
    {
        $commentaire = $commentaireRepository->find((int)$request->get("id"));

        if (!$commentaire) {
            return new JsonResponse(null, 404);
        }

        
            $poste = $posteRepository->find((int)$request->get("poste"));
        if (!$poste) {
        return new JsonResponse("poste with id " . (int)$request->get("poste") . " does not exist", 203);
        }
        
            $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
        return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }
        
        

        $commentaire->constructor(
            $poste,
            $user,
            $request->get("description"),
            DateTime::createFromFormat("d-m-Y", $request->get("date"))
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return new JsonResponse($commentaire, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $commentaire = $commentaireRepository->find((int)$request->get("id"));

        if (!$commentaire) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($commentaire);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
}
