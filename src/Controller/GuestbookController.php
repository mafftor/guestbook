<?php

namespace App\Controller;

use App\Entity\Guestbook;
use App\Form\GuestbookFormType;
use App\Repository\GuestbookRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuestbookController extends AbstractController
{
    public function __construct(
        private readonly GuestbookRepository $guestbookRepository,
        private readonly Filesystem          $filesystem,
    )
    {
    }

    #[Route('/guestbooks', name: 'guestbooks_store', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $guestbook = new Guestbook();
        $form = $this->createForm(GuestbookFormType::class, $guestbook);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Guestbook $guestbook */
            $guestbook = $form->getData();

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();

            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';

            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );

            $guestbook->setImage($newFilename);
            $guestbook->setCreatedAt(new DateTimeImmutable());
            $guestbook->setAuthor($this->getUser());

            $this->guestbookRepository->save($guestbook, true);

            $this->addFlash(
                'success',
                'You have successfully created the guestbook'
            );
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/guestbook/status/{id}', name: 'app_sad', methods: ['POST'])]
    public function changeStatus(Guestbook $guestbook, Request $request)
    {
        if ($request->get('type')) {
            $guestbook->setApproved(true);
            $this->guestbookRepository->save($guestbook, true);
            $message = 'approved';
        } else {
            $this->guestbookRepository->remove($guestbook, true);
            $message = 'removed';

            $this->filesystem->remove($this->getParameter('kernel.project_dir') . '/public/uploads/' . $guestbook->getImage());
        }

        $this->addFlash(
            'success',
            sprintf('You have successfully %s the guestbook', $message)
        );

        return $this->redirectToRoute('app_home');
    }
}
