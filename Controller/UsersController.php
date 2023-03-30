<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserFormType;
use App\Form\ModifUserType;
use App\Form\RechercheUserType;
use App\Form\TriformType;
use App\Form\SearchUserType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
// use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use  Swift_Mailer;
use Swift_Message;
use Symfony\Component\Mime\Address;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Flex\Response as FlexResponseee;

use Dompdf\Dompdf;
use Dompdf\Options;


class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function index(): Response
    {
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }
    #[Route('/signup', name: 'app_addU')]
    public function ajouterUser(UsersRepository $r,ManagerRegistry $doctrine,Request $request): Response
    {
        // $em=$doctrine->getManager();
         $user = new Users();
         $form=$this->createForm(UserFormType::class,$user);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()){
           $em=$this->getDoctrine()->getManager();
           $em->persist($user);
           $em->flush();
         }



        return $this->renderForm('users/signup.html.twig', 
            array('users'=>$form));
    }

    #[Route('/afficherUsers', name: 'app_afficherU')]
    public function afficherUsers(Request $request,UsersRepository $userRepo,ManagerRegistry $doctrine): Response
    {
       $users=$this->getDoctrine()->getRepository(Users::class)->findAll(); 
       $UserTrie =$userRepo->findAlltri();
    //    $findName=$this->getDoctrine()->getRepository(Users::class);
    //     $formSearchName=$this->createForm(SearchUserType::class);
    //     $formSearchName->handleRequest($request);
        $formtri=$this->createForm(TriformType::class);
        $formtri->handleRequest($request);
    $search = $this->createForm(RechercheUserType::class);
        
        $search->handleRequest($request);
         
         if($search->isSubmitted() && $search->isValid()){
           
            $users = $userRepo->search(
                $search->get('mots')->getData() );

        return $this->render('users/afficherUsers.html.twig', [
            'users' => $users, 'form' => $search->createView(),'formtri' => $formtri->createView()
        ]);

        // $users=$userRepo->search($search->getData());
         }
         else if ($formtri->isSubmitted()) {

            return $this->render('users/afficherUsers.html.twig', ['users' => $UserTrie, 'form' => $search->createView(),'formtri' => $formtri->createView()]);
        }
       // $users = $userRepo->search("Chef Entreprise ");

        return $this->render('users/afficherUsers.html.twig', [
            'users' => $users, 'form' => $search->createView(),'formtri' => $formtri->createView()
        ]);

   
    }

    #[Route('/suppU/{id}', name: 'suppUser')]
    public function suppU($id,UsersRepository $r ,ManagerRegistry $doctrine): Response
    {
        $user=$r->find($id);
        $em=$doctrine->getManager();
        $em->remove($user);
        $em->flush();


        return $this->redirectToRoute('app_afficherU' );
    }



    #[Route('/modiferUsers/{id}', name: 'UpadateUser')]
    public function modifierUser($id,UsersRepository $r ,ManagerRegistry $doctrine,Request $request): Response
    {
        // boutton te5o el id taa el user eli nzelt alih bich tmodifih
        $user=$r->find($id);
     
        $form=$this->createForm(ModifUserType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
          $em=$doctrine->getManager();
          $em->persist($user);
          $em->flush();
          return $this->redirectToRoute('app_afficherU');

        }

        return $this->renderForm('users/modiferUsers.html.twig', 
        array('users'=>$form));
    }

    #[Route('/afficherUserById/{id}', name: 'app_afficherById')]
    public function afficherUsersById($id,UsersRepository $r): Response
    {
        
       $user=$r->find($id);
       

        return $this->render('users/afficherUserById.html.twig', [
            'users' => $user,
        ]);
    }


    public function indexx(UsersRepository $userRepository,SessionInterface $session): Response
    {   $user=$session->get('users');
        
        if(is_null($user)){
            return $this->redirectToRoute('app_users');

        }else if($user->getRole()!="admin")
        {   return $this->redirectToRoute('app_addU');
        }

        return $this->render('user/afficherUsers.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/Admin', name: 'display_admin')]
    public function indexAdmin(): Response
    {   
        return $this->render('Admin/index.html.twig');
    }
    #[Route('/Acceuil', name: 'display_client')]
    public function indexClient(): Response
    {   
        return $this->render('Client/index.html.twig');



    }

    #[Route('/afficherUserBySession', name: 'display_sessionClient')]
    public function indexClientProfile(SessionInterface $session,Request $request,UsersRepository $r,UserRepository $rr): Response
    {  

         $user=new Users();

         

        // $emailSS=$user->getEmail();

     

         
        // $user=$session->get('user');
        // if(is_null($user)){
        //     return $this->redirectToRoute('display_admin');
        //     //ijiblik fil user nulll !!!

        // }

        // $email=$rr->findByEmail($emailSS);

        // $user3adi=$r->findBy($email);

        // $user3adi= $this->getDoctrine()->getRepository(UserRepository::class)
        // ->find($user->getMail());


//['user'=>$user3adi]




        return $this->render('users/profil.html.twig');


    }

    #[Route('/Admin/stats', name: 'stats')]
    public function statistiques(UsersRepository $r): Response 
    {
        $users=$r->findAll();

        $userRole =[];
        $userRole2 =[];
        $userRole3 =[];
        $userRoleCount = [];
        $userRoleCountCandidat=0;
        $userRoleCountNone=0;
        $userRoleCountChef=0;

        foreach($users as $users){
            if ($users->getRole()=="Candidat") {
                $userRole []= $users->getRole();
                
                // echo(count($userRole));
                // $userRoleCountCandidat[] = count( $userRole);
                $userRoleCountCandidat = count($userRole);
              
                
           }
            else if ($users->getRole()=="none") {
              
               $userRole2[] = $users->getRole();
               $userRoleCountNone=count($userRole2);

          
               
           
        }

        else if ($users->getRole()=="Chef Entreprise") {
              
            $userRole3[] = $users->getRole();
            $userRoleCountChef=count($userRole3);

       
            
        
     }

          
        }

        return $this->render('Admin/stats.html.twig', [
            'userRoleCount' => json_encode($userRoleCount),
            'userRole' => json_encode("userRole"),
            'userRoleE' => json_encode("ChefEntreprise"),
            'userRoleC' => json_encode("Candidat"),
            'userRoleN' => json_encode("None"),
            'userRoleCountNone' => json_encode($userRoleCountNone),
            'userRoleCountCandidat' => json_encode($userRoleCountCandidat),
            'userRoleCountChef' => json_encode($userRoleCountChef),

         
        ]);
    }

    
    // #[Route('/Admin/mailsender', name: 'mailsender')]
    // public function mailsender(MailerInterface $mailer): Response
    // {
    //     $email=(new Email())
    //         ->from('benzaahamza2222@gmail.com')
    //         ->to('benamor.hamza@esprit.tn')
    //         ->subject('send')
    //         ->text('hamza');
       
    //     return $this->redirectToRoute('mailsender');

    //     try {
    //         $mailer->send($email);
    //     } catch (TransportExceptionInterface $e) {
            
    //     }

    // }

    #[Route('/Admin/mailsenderSwift', name: 'mailsenderSwift')]
    public function mailsenderSwift(Swift_Mailer $mailer): Response
    {
    $message = (new Swift_Message('Mot de password oublié'))
    ->setFrom('benzaahamza2222@gmail.com')
    ->setTo('benamor.hamza@esprit.tn');
  


    $mailer->send($message);
    return $this->redirectToRoute('display_admin');

    }

    #[Route('/pdf',name:'pdfuser')]
    public function pdfuser()
    {
        $Userfind = $this->getDoctrine()->getRepository(User::class)->findAll();
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('users/pdf.html.twig', [
            'user'=>$Userfind, 'title'=>"Pdf users test"
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }


    // #[Route('/email')]
    // public function sendEmail(MailerInterface $mailer): Response
    // {
    //     $email = (new Email())
    //         ->from('hamzabenz2002@gmail.com')
    //         ->to('benahamza279@gmail.com')
    //         ->subject('Time for Symfony Mailer!')
    //         ->text('Sending emails is fun again!')
    //         ->html('<p>See Twig integration for better HTML integration!</p>');

    //     $mailer->send($email);

    //     // ...
    // }
}