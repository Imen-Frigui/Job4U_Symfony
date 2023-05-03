<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ProjectRepository;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'display_project')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $projects = $doctrine
            ->getRepository(project::class)->findAll();
        return $this->render(
            'project/affProject.html.twig',
            [
                'c' => $projects

            ]
        );
    }

    #[Route('/addProject', name: 'addProject')]
    public function addOffre(ManagerRegistry $doctrine, Request $request)
    {
        $project = new project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($project); //add
            $em->flush();
            return $this->redirectToRoute("display_project");
        }
        return $this->render('project/addProject.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    #[Route('/admin/addProject', name: 'addProject')]
    public function AdminaddOffre(ManagerRegistry $doctrine, Request $request)
    {
        $project = new project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($project); //add
            $em->flush();
            return $this->redirectToRoute("display_project");
        }
        return $this->render('project/AdminProject.html.twig', [
            'f' => $form->createView(),
        ]);
    }


    #[Route('/suppProject/{id}', name: 'suppP')]
    public function suppOffre(
        $id,
        ProjectRepository $r,
        ManagerRegistry $doctrine
    ): Response { //récupérer la classroom à supprimer
        $project = $r->find($id);
        //Action suppression
        $em = $doctrine->getManager();
        $em->remove($project);
        $em->flush();
        return $this->redirectToRoute('display_project');
    }

    #[Route('/updateP/{id}', name: 'updateProject')]
    public function modifierOffre(ManagerRegistry $doctrine, Request $request, $id, ProjectRepository $r)
    { { //récupérer la classroom à modifier
            $project = $r->find($id);
            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $doctrine->getManager();
                $em->flush();
                return $this->redirectToRoute("display_project");
            }
            return $this->renderForm(
                "project/addProject.html.twig",
                array("f" => $form)
            );
        }
    }
}
