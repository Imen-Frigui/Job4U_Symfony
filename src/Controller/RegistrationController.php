<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Users;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    
    #[Route('/registration', name: 'registration')]
    public function index(Request $request)
    {
        $user = new User();
        $users = new Users();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $user->setPassword($this->passwordEncoder->encodePassword($user,$user->getPassword()));
            // $images = $form->get('image')->getData();

            // Set their role
            $user->setRoles(['ROLE_USER']);
    ////////////////////////////////////////////////////////////////////////////////////////
      /** @var UploadedFile $uploadedFile */
      $uploadedFile = $form['image']->getData();
      $destination = 'asset1/img/';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $destination . $originalFilename.'.' . $uploadedFile->guessExtension();

      if($uploadedFile){$uploadedFile->move(
        $destination,
        $newFilename);
        }

        $user->setImage($newFilename);


  

     /////////////////////////////////////////////////////////////////////////////////////      
            $users->setRole("Candidat");
            $users->setPassword($this->passwordEncoder->encodePassword($user,$user->getPassword()));
            $users->setNom($user->getName());
            $users->setPrenom($user->getName());
            $users->setMail($user->getEmail());
            $user->getId($users->getId());

            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($users);
            $em->persist($user);
            
            $em->flush();

            return $this->redirectToRoute('captcha');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}