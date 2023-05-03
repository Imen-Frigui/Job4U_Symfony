<?php

namespace App\Controller;

use App\Entity\Likez;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Poste;
use App\Entity\Rating;
use App\Form\ReportType;
use App\Entity\Report;
use App\Entity\User;
use App\Form\PosteType;
use App\Repository\PosteRepository;
use App\Repository\CommentaireRepository;
use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Knp\Component\Pager\Paginator;

#[Route('/poste')]
class PosteController extends AbstractController
{
    #[Route('/posts', name: 'app_poste_index', methods: ['GET'])]
    public function index(Request $request, PosteRepository $posteRepository, PaginatorInterface $paginator): Response
    {


        return $this->render('poste/index.html.twig', [
            'postes' => $posteRepository->findAll(),
        ]);
    }
    #[Route('/forum', name: 'app_poste_indexFront', methods: ['GET'])]
    public function indexFront(Request $request, PosteRepository $posteRepository): Response
    {
        $domaine = $request->query->get('domaine');
        $searchTerm = $request->query->get('search');

        $repository = $this->getDoctrine()->getRepository(Poste::class);
        $queryBuilder = $repository->createQueryBuilder('p');

        if ($domaine) {
            $queryBuilder
                ->andWhere('p.domaine = :domaine')
                ->setParameter('domaine', $domaine);
        }

        if ($searchTerm) {
            $queryBuilder
                ->andWhere('p.titre LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $postes = $queryBuilder->getQuery()->getResult();

        return $this->render('poste/indexFront.html.twig', [
            'postes' => $postes,
            'domaine' => $domaine,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/addposts', name: 'app_poste_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PosteRepository $posteRepository, UserRepository $userrepo): Response
    {
        $poste = new Poste();
        $user = $this->getUser();


        $poste->setIduser($user);
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('img')->getData();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );

            // ... other code to handle the $post object

            $poste->setImg($fileName);
            $poste->setDate(date('Y-m-d H:i:s'));
            // ...
            $posteRepository->save($poste, true);

            return $this->redirectToRoute('app_poste_indexFront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poste/new.html.twig', [
            'poste' => $poste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_poste_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Poste $poste, CommentaireRepository $commentrepo, UserRepository $userrepo): Response
    {
        $user = $this->getUser();
        //$user = $userrepo->findOneById(1);
        $check = $poste->isLikeByUser($user);
        $rating = new Rating();
        $form = $this->createFormBuilder($rating)
            ->add('note', ChoiceType::class, [
                'label' => 'Rating',
                'choices' => [
                    '1 star' => 1,
                    '2 stars' => 2,
                    '3 stars' => 3,
                    '4 stars' => 4,
                    '5 stars' => 5,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the rating
            $user = $this->getUser();

            $rating->setIdUser($user);
            $rating->setIdPoste($poste);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->flush();

            // Redirect to the same page to prevent form resubmission
            return $this->redirectToRoute('app_poste_show', ['id' => $poste->getId()]);
        }
        $averageRating = $this->getDoctrine()
            ->getRepository(Rating::class)
            ->getAverageRatingForPost($poste->getId());

        return $this->render('poste/show.html.twig', [
            'poste' => $poste,
            'commentaires' => $commentrepo->allcomments($poste->getId()),
            'check' => $check,
            'rating_form' => $form->createView(),
            'average_rating' => $averageRating,
        ]);
    }
    #[Route('/{id}/showback', name: 'app_poste_showBack', methods: ['GET'])]
    public function showBack(Poste $poste, CommentaireRepository $commentrepo): Response
    {
        return $this->render('poste/showBack.html.twig', [
            'poste' => $poste,
            'commentaires' => $commentrepo->allcomments($poste->getId()),
        ]);
    }

    #[Route('/poste/delete/{id}', name: 'poste_delete')]
    public function deletePost(Poste $poste): Response
    {

        $em = $this->getDoctrine()->getManager();

        $em->remove($poste);
        $em->flush();
        $this->addFlash('success', 'The event has been deleted successfully.');

        return $this->redirectToRoute('app_poste_indexFront');
    }
    #[Route('/poste/deleteB/{id}', name: 'poste_deleteB')]
    public function deletePostB(Poste $poste): Response
    {

        $em = $this->getDoctrine()->getManager();

        $em->remove($poste);
        $em->flush();
        $this->addFlash('success', 'The event has been deleted successfully.');

        return $this->redirectToRoute('app_poste_index');
    }



    #[Route('/{id}/like', name: 'app_poste_like', methods: ['GET', 'POST'])]
    public function like(Poste $poste, EntityManagerInterface $entityManager, UserRepository $userrepo)
    {
        $like = new Likez();
        $user = $this->getUser();
        $id = $poste->getId();

        //$user = $userrepo->findOneById(1);
        $like->setIdUser($user);
        $like->setIdPoste($poste);
        $check = $poste->isLikeByUser($user);
        $entityManager->persist($like);
        $entityManager->flush();

        $this->addFlash('success', 'Like ajouté avec succès!');

        return $this->redirectToRoute(
            'app_poste_show',
            ['id' => $id]
        );
    }
    #[Route('/{id}/dislike', name: 'app_poste_dislike', methods: ['GET', 'POST'])]
    public function dislike(Poste $poste, EntityManagerInterface $entityManager, UserRepository $userrepo)
    {
        $user = $this->getUser();

        //$user = $userrepo->findOneById(1);
        $like = $entityManager->getRepository(Likez::class)->findOneBy([
            'idUser' => $user,
            'idPoste' => $poste->getId(),
        ]);
        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
            $this->addFlash('success', 'Dislike avec succès!');
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas enlever un like que vous n\'avez pas mis.');
        }
        return $this->redirectToRoute(
            'app_poste_show',
            ['id' => $id]
        );
    }

    #[Route('/{id}/edit', name: 'app_poste_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Poste $poste, PosteRepository $posteRepository): Response
    {
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('img')->getData();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );

            // ... other code to handle the $post object

            $poste->setImg($fileName);
            $posteRepository->save($poste, true);

            return $this->redirectToRoute('app_poste_indexFront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poste/edit.html.twig', [
            'poste' => $poste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/report', name: 'app_poste_report', methods: ['POST', 'GET'])]
    public function report(Request $request, Poste $poste, EntityManagerInterface $entityManager, UserRepository $userRepo)
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            //$user = $userRepo->findOneById(1);
            $report->setIdPoste($poste);
            $report->setIdUser($user);
            $entityManager->persist($report);
            $entityManager->flush();

            $this->addFlash('success', 'Le signalement a été envoyé avec succès!');
            return $this->redirectToRoute('app_poste_indexFront');
        }

        return $this->render('poste/report.html.twig', [
            'poste' => $poste,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: 'app_poste_delete', methods: ['POST'])]
    public function delete(Request $request, Poste $poste, PosteRepository $posteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $poste->getId(), $request->request->get('_token'))) {
            $posteRepository->remove($poste, true);
        }

        return $this->redirectToRoute('app_poste_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/del', name: 'app_poste_deleteFront', methods: ['POST'])]
    public function deleteFront(Request $request, Poste $poste, PosteRepository $posteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $poste->getId(), $request->request->get('_token'))) {
            $posteRepository->remove($poste, true);
        }

        return $this->redirectToRoute('app_poste_indexFront', [], Response::HTTP_SEE_OTHER);
    }
}
