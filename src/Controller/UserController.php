<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[Route('/users/admin', name: 'users_admin', methods: ['POST'])]
    public function store(Request $request)
    {
        $user = $this->getUser();

        $user = $this->userRepository->findOneByIdentifier($user->getUserIdentifier());

        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $user->setRoles([]);
        } else {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->userRepository->save($user, true);

        $this->addFlash(
            'success',
            'You changed a user mode!'
        );

        return $this->redirectToRoute('app_home');
    }
}
