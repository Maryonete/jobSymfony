<?php

namespace App\Controller;

use App\Entity\Offre;
use Twig\Environment;
use App\Form\OffreType;
use App\Service\UrlCheckerService;
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
    public function __construct(
        private OffreRepository $offreRepository,
        private UrlCheckerService $urlChecker
    ) {}

    #[Route('/{filter<NON|ATT>?}', name: 'app_home', methods: ['GET'])]
    #[Route('/{checkUrls<1>?}', name: 'app_home', methods: ['GET'])]
    public function index(?string $filter = null, ?int $checkUrls = null): Response
    {
        $offres = $this->getFilteredOffres($filter);

        if ($checkUrls) {
            $this->validateOffresUrls($offres);
        }
        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    private function getFilteredOffres(?string $filter): array
    {
        return match ($filter) {
            'ATT' => $this->offreRepository->findBy(
                ['reponse' => ['ATT', '']],
                ['id' => 'DESC']
            ),
            'NON' => $this->offreRepository->findBy(
                ['reponse' => 'NON'],
                ['id' => 'DESC']
            ),
            default => $this->offreRepository->findBy(
                [],
                ['id' => 'DESC']
            )
        };
    }

    private function validateOffresUrls(array $offres): void
    {
        foreach ($offres as $offre) {

            if ($this->shouldValidateUrl($offre)) {
                $offre->setIsUrlValid($this->urlChecker->isUrlValid($offre->getUrl()));
            }
        }
    }

    private function shouldValidateUrl(Offre $offre): bool
    {

        return $offre->getUrl() && $offre->getReponse() !== 'NON';
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
