<?php

namespace App\Controller;

use App\Entity\User;
use Twilio\Serialize;
use App\Form\UserType;
use PhpParser\Builder\Method;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// #[IsGranted('ROLE_ADMIN')]
#[Route('/users')]
class UserController extends AbstractController
{
    private $em;
    private $userRepository;
    private $serializer;
    public function __construct(EntityManagerInterface $em, UserRepository $userRepository, SerializerInterface $serializer) 
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        // $encoders = [new XmlEncoder(), new JsonEncoder()];
        // $normalizers = [new ObjectNormalizer()];
        $this->serializer = $serializer;
    }
    #[Route('/admin', name: 'app_user_list')]
    public function index(Request $request): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $numberOfUsersPerPage = 5;
        $totalUsers = count($users);
        $totalPages = ceil($totalUsers / $numberOfUsersPerPage);
        $pageNumber = $request->query->getInt('page', 1);
        $offset = ($pageNumber - 1) * $numberOfUsersPerPage;
        $limit = $numberOfUsersPerPage;
        $usersOnCurrentPage = array_slice($users, $offset, $limit);
    
        return $this->render('BackOffice/user/index.html.twig', [
            'users' => $usersOnCurrentPage,
            'totalPages' => $totalPages,
            'currentPage' => $pageNumber,
        ]);
    }


    #[Route('/create', name: 'app_user_create')]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('BackOffice/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show')]
    public function show($id): Response
    {
        return $this->render('BackOffice/user/show.html.twig', [
            'user' => $this->userRepository->find($id),
        ]);
    }

    #[Route('/{id}/profile', name: 'app_user_show_na')]
    public function show_user($id): Response
    {
        return $this->render('FrontOffice/user/show.html.twig', [
            'user' => $this->userRepository->find($id),
        ]);
    }

    #[Route('/{id}/edit/admin', name: 'app_user_edit')]
    public function edit(Request $request, $id): Response
    {
        $user = $this->userRepository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('app_user_list');
        }

        return $this->renderForm('BackOffice/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit')]
    public function edit_user(Request $request, $id): Response
    {
        $user = $this->userRepository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('app_user_list');
        }

        return $this->renderForm('FrontOffice/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/archive', name:'app_user_archive')]
    public function archive($id): Response
    {
        $user = $this->userRepository->find($id);
        $user->setIsArchived(true);
        $this->em->flush();

        return $this->redirectToRoute('app_user_list');
    }

    #[Route('/{id}/ban', name:'app_user_ban')]
    public function ban($id): Response
    {
        $user = $this->userRepository->find($id);
        $user->setIsBanned(true);
        $this->em->flush();

        return $this->redirectToRoute('app_user_list');
    }

    #[Route('/{id}/unban', name:'app_user_unban')]
    public function unban($id): Response
    {
        $user = $this->userRepository->find($id);
        $user->setIsBanned(false);
        $this->em->flush();

        return $this->redirectToRoute('app_user_list');
    }

    /* ************************************ */

    #[Route('/adduser')]
    public function addUser(Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user = new User();
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $name = $request->query->get("name");
        $number = $request->query->get("number");
        $age = $request->query->get("age");
        $location = $request->query->get("location");
        $donationStatus = $request->query->get("donationStatus");
        $bloodType = $request->query->get("bloodType");
        $userRole = $request->query->get("userRole");
        
        $user->setEmail($email);
        // $userPasswordHasher = new UserPasswordHasherInterface();
        $user->setPassword($userPasswordHasher->hashPassword($user,$password));
        $user->setName($name);
        $user->setNumber($number);
        $user->setAge($age);
        $user->setLocation($location);
        $user->setBloodType($bloodType);
        $user->setDonationStatus($donationStatus);
        $user->setUserRole($userRole);
        
        $this->em->persist($user);
        $this->em->flush();

        // $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $this->serializer->serialize($user, 'json', ['groups' => "users"]);
        return new JsonResponse($formatted);
        
    }

    #[Route('/userarchive')]
    public function archiveUser(Request $request): Response
    {
        $id = $request->get("id");

        $user = $this->userRepository->find($id);
        if($user != null)
        {
            $user->setIsArchived(true);
            $this->em->flush();
            
            $formatted = $this->serializer->serialize("User archived successfully!",'json');
            return new JsonResponse($formatted);
        }

        return new JsonResponse("User not found!");
    }

    #[Route('/edituser')]
    public function editUser(Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user = $this->userRepository->find($request->get("id"));
        if($user != null)
        {
            $email = $request->query->get("email");
            $password = $request->query->get("password");
            $name = $request->query->get("name");
            $number = $request->query->get("number");
            $age = $request->query->get("age");
            $location = $request->query->get("location");
            $donationStatus = $request->query->get("donationStatus");
            $bloodType = $request->query->get("bloodType");
            $userRole = $request->query->get("userRole");
            
            $user->setEmail($email);
            // $userPasswordHasher = new UserPasswordHasherInterface();
            $user->setPassword($userPasswordHasher->hashPassword($user,$password));
            $user->setName($name);
            $user->setName($number);
            $user->setAge($age);
            $user->setLocation($location);
            $user->setDonationStatus($donationStatus);
            $user->setBloodType($bloodType);
            $user->setUserRole($userRole);

            $this->em->flush();
            
            $formatted = $this->serializer->serialize("User updated successfully!",'json');
            return new JsonResponse($formatted);
        }

        return new JsonResponse("User not found!");
    }

    #[Route('/display')]
    public function displayUser()
    {
        $users = $this->userRepository->findAll();
 
        $formatted = $this->serializer->serialize($users, 'json', ['groups' => "users"]);
        return new JsonResponse($formatted);

    }

    #[Route('/show')]
    public function showUser(Request $request)
    {
        $user = $this->userRepository->find($request->get("id"));
        if($user != null)
        {
            $formatted = $this->serializer->serialize($user, 'json', ['groups' => "users"]);
            return new JsonResponse($formatted);
        }
        return new JsonResponse("User not found!");
    }
}
