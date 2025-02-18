<?php

namespace App\Controller;

use App\Entity\Offre;
use Twig\Environment;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
final class OffreController extends AbstractController
{
    #[Route('/{reponse<NON|OUI|ATT>?}', name: 'app_home', methods: ['GET'])]
    public function index(
        OffreRepository $offreRepository,
        ?string $reponse
    ): Response {
        $offres = [];
        if ($reponse == 'ATT') {
            $offres = $offreRepository->findBy(['reponse' => [$reponse, '']], ['id' => 'DESC']);
        } elseif ($reponse == 'NON') {
            $offres = $offreRepository->findBy(['reponse' => $reponse], ['id' => 'DESC']);
        } else {
            $offres = $offreRepository->findBy([], ['id' => 'DESC']);
        }
        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/find/{find}', name: 'app_offre_find', methods: ['POST'])]
    public function find(string $find, OffreRepository $offreRepository, Environment $twig): Response
    {
        $offres = $offreRepository->findByMotsCles($find);

        return new Response($twig->render('offre/_tbody.html.twig', [
            'offres' => $offres
        ]));
    }
}
