<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;
use App\Repository\PosteRepository;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository, NotificationRepository $notificationRepository): Response
    {
        $user =$this->getUser();
        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasRead' => false,
        ]);
        return $this->render('commentaire/index.html.twig', [
            'notifications' => $notifications,
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }
    #[Route('/postcomments', name: 'app_commentaire_index', methods: ['GET'])]
    public function indexpost(CommentaireRepository $commentaireRepository, NotificationRepository $notificationRepository): Response
    {
        $user =$this->getUser();
        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasRead' => false,
        ]);
        return $this->render('commentaire/showposts.html.twig', [
            'commentaires' => $commentaireRepository->allcomments(),
            'notifications' => $notifications
        ]);
    }

    #[Route('/new', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommentaireRepository $commentaireRepository, UserRepository $ur, PosteRepository $pr): Response
    {
        $commentaire = new Commentaire();
        $commentaire->setIdPoste($pr->find(intval($request->request->get("id-poste"))));
        $commentaire->setDescription($request->request->get("comm-content"));
        $commentaire->setIdUser($ur->find(1));
        $commentaire->setDate(date('Y-m-d H:i:s'));
        $commentaireRepository->save($commentaire, true);
        return $this->render('commentaire/show-comment.html.twig', ["c"=>$commentaire]);
    }

    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaireRepository->save($commentaire, true);

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $commentaireRepository->remove($commentaire, true);
        }

        return $this->redirectToRoute('app_poste_indexFront', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/delback', name: 'app_commentaire_deleteBack', methods: ['POST'])]
    public function deleteBack(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $commentaireRepository->remove($commentaire, true);
        }

        return $this->redirectToRoute('app_poste_index', [], Response::HTTP_SEE_OTHER);
    }
}
