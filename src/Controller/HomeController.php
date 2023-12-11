<?php

namespace App\Controller;

use App\Entity\Guestbook;
use App\Form\GuestbookFormType;
use App\Repository\GuestbookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly GuestbookRepository $guestbookRepository,
    )
    {
    }

    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $guestbook = new Guestbook();
        $form = $this->createForm(GuestbookFormType::class, $guestbook, [
            'action' => $this->generateUrl('guestbooks_store'),
            'method' => 'POST',
        ]);

        $guestbooks = $this->guestbookRepository->getLastItems(!$this->getUser() || !$this->isGranted('ROLE_ADMIN'));

        return $this->render('home/index.html.twig', [
            'form' => $form,
            'guestbooks' => $guestbooks,
        ]);
    }
}
