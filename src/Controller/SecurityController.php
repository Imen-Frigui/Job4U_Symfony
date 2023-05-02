<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use App\Entity\Captcha;
use App\Form\CaptchaUserType;

// use  Swift_Mailer;
// use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;



class SecurityController extends AbstractController
{

     
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        
         

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();


        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();



        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout():  \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->redirectToRoute("app_login");
    }

     
    #[Route(path: '/forgot', name: 'forgot')]
    public function forgotPassword(Request $request, UserRepository $userRepository,MailerInterface $mailer, TokenGeneratorInterface  $tokenGenerator)
    {

       

        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
           
        
            $donnees=$form->get('email')->getData();
            $user=$this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=>$donnees]);
          
          //  $user = $userRepository->find($donnees);
           

            if(!$user) {
                $this->addFlash('danger','cette adresse n\'existe pas');
                return $this->redirectToRoute("forgot");

            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $entityManger = $this->getDoctrine()->getManager();
                $entityManger->persist($user);
                $entityManger->flush();




            }catch(\Exception $exception) {
                $this->addFlash('warning','une erreur est survenue :'.$exception->getMessage());
                return $this->redirectToRoute("app_login");


            }

            $url = $this->generateUrl('app_reset_password',array('token'=>$token),UrlGeneratorInterface::ABSOLUTE_URL);

        //        //BUNDLE MAILER
        //     $message = (new Swift_Message('Mot de password oublié'))
        //         ->setFrom('jobforyou548@gmail.com')
        //       ->setTo($user->getEmail())
        //     ->setBody("<p> Bonjour</p> unde demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant :".$url,
        //      "text/html");

        // //    //send mail
        //   $mailer->send($message);

        /////////////////////////////////////////:btari9a o5ra bil Mailer

        // $transport = Transport::fromDsn('smtp://jobforyou548@gmail.com:tsxlyqvduzkyasee@smtp.gmail.com:587');
        // $mailer = new Mailer($transport);
        $email=(new Email())
                ->from('hamzaemailtest2323@gmail.com')
                ->to($user->getEmail())
                ->subject('Voici votre Token! veuillez copier le lien ci dessous')
                
                ->html('<p> Bonjour</p> unde demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant :"'.$url,'
                     text/html');

        $mailer->send($email);
        
      
        // try {
        //     $mailer->send($email);
        // } catch (TransportExceptionInterface $e) {
            
        // }

        $this->addFlash('message','un code de vérification vous vous être envoyé pour confirmer votre identité ');



      
          
            
          // return $this->redirectToRoute("app_login");



        }

        return $this->render("security/forgotPassword.html.twig",['form'=>$form->createView()]);
    }


   
    #[Route(path: '/resetpassword/{token}', name: 'app_reset_password')] 
    public function resetpassword(Request $request,string $token, UserPasswordEncoderInterface  $passwordEncoder) : Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token'=>$token]);

        if($user == null ) {
            $this->addFlash('danger','TOKEN INCONNU');
            return $this->redirectToRoute("app_login");

        }
        
        if($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword($passwordEncoder->encodePassword($user,$request->request->get('password')));
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($user);
            $entityManger->flush();

            $this->addFlash('message','Mot de passe mis à jour :');
            return $this->redirectToRoute("app_login");

        }
        else {
            return $this->render("security/resetPassword.html.twig",['token'=>$token]);

        }
    }

   ////////////////////////////////////////////: Captcha://////////////////////////////////////////////
 #[Route('/captcha', name: 'captcha')]
 public function captcha(Request $request) 
 {
 
     $x=random_int(1,11);
     $Captcha = $this->getDoctrine()->getRepository(Captcha::class)->find($x);
     $formCaptcha= $this->createForm(CaptchaUserType::class);
     $formCaptcha->add('id', HiddenType::class,['data' =>$x]);
     $formCaptcha->handleRequest($request);

     if ($formCaptcha->isSubmitted()) {
         $data=$formCaptcha->getData();
         $findCaptcha=$this->getDoctrine()->getRepository(Captcha::class)->find($data['id']);
         $verif=$data['Captcha'];
         if($findCaptcha->getValue()==$verif)
         {return $this->redirectToRoute('app_login');}
     }
     $x=random_int(1,11);
     $Captcha = $this->getDoctrine()->getRepository(Captcha::class)->find($x);
     $formCaptcha= $this->createForm(CaptchaUserType::class);
     $formCaptcha->add('id', HiddenType::class,['data' =>$x]);

     return $this->render('users/CaptchaUser.html.twig', ['captcha'=>$Captcha,'formCaptcha' =>$formCaptcha->createView()]);
 }

 




 /////////////////////////////////////////////////////////////////////////////////////////:


    //////////////////////::Email Test ::////////////////////////////////////////////////////////////////////::

    // #[Route('/new/{id}', name: 'app_don_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, MailerInterface $mailer, DonRepository $donRepository,MembreRepository $MembreRepository,Association $association): Response
    // {
    //     $session = $request->getSession();
    //     $membre = $session->get('user');
    //     $membre=$MembreRepository->find($membre->getId());
    //     $don = new Don();
    //     $don->setAssociation($association);
    //     $don->setMembre($membre);
    //     $form = $this->createForm(DonType::class, $don);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $donRepository->save($don, true);
    //         $transport = Transport::fromDsn('smtp://aura.donation@gmail.com:yqqtxjlvihujnscn@smtp.gmail.com:587');
    //         $mailer = new Mailer($transport);
    //         $email = (new Email())
    //             ->from('aura.donation@gmail.com')
    //             ->to($don->getEmail())
    //             ->subject('Thank you for your donation!')
    //             ->html('<p style="color: #000000; background-color: #0073ff; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">Dear donor, thank you for your generous donation of ' . $don->getMontant() . ' € !</p>');
    //         /*
    //             // Set HTML "Body"
    //             $email->html('
    //                 <h1 style="color: #fff300; background-color: #0073ff; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
    //                 The HTML version of the message.
    //                 </h1>
    //                 <img src="cid:Image_Name_1" style="width: 600px; border-radius: 50px">
    //                 <br>
    //                 <img src="cid:Image_Name_2" style="width: 600px; border-radius: 50px">
    //                 <h1 style="color: #ff0000; background-color: #5bff9c; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
    //                 The End!
    //                 </h1>
    //             ');

    //             // Add an "Attachment"
    //             $email->attachFromPath('example_1.txt');
    //             $email->attachFromPath('example_2.txt');

    //             // Add an "Image"
    //             $email->embed(fopen('image_1.png', 'r'), 'Image_Name_1');
    //             $email->embed(fopen('image_2.jpg', 'r'), 'Image_Name_2');
    //             */
    //         $mailer->send($email);

    //         return $this->redirectToRoute('app_don_thanks', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('don/new.html.twig', [
    //         'don' => $don,
    //         'form' => $form,
    //         'user' => $membre
    //     ]);
    // }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
