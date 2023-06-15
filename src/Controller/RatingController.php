<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\Article;
use App\Form\RatingType;
use App\Form\ArticleType;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/rating')]
class RatingController extends AbstractController
{
    #[Route('/', name: 'app_rating_index', methods: ['GET'])]
    public function index(RatingRepository $ratingRepository): Response
    {
        return $this->render('rating/index.html.twig', [
            'ratings' => $ratingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rating_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RatingRepository $ratingRepository): Response
    {
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ratingRepository->save($rating, true);

            return $this->redirectToRoute('app_rating_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rating/new.html.twig', [
            'rating' => $rating,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rating_show', methods: ['GET'])]
    public function show(Rating $rating): Response
    {
        return $this->render('rating/show.html.twig', [
            'rating' => $rating,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rating_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rating $rating, RatingRepository $ratingRepository): Response
    {
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ratingRepository->save($rating, true);

            return $this->redirectToRoute('app_rating_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rating/edit.html.twig', [
            'rating' => $rating,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rating_delete', methods: ['POST'])]
    public function delete(Request $request, Rating $rating, RatingRepository $ratingRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rating->getId(), $request->request->get('_token'))) {
            $ratingRepository->remove($rating, true);
        }

        return $this->redirectToRoute('app_rating_index', [], Response::HTTP_SEE_OTHER);
    }
/*#[Route('/{id}/note', name: 'app_rating_note', methods: ['GET', 'POST'])]
 
public function ratingUpdateAction(Request $request , EntityManagerInterface $em , Rating $rating)
{
    
    // Récupération de la note à partir de la variable POST
    $note = $request->request->get('note');

    // Mise à jour de la propriété "value" de l'entité "Rating"
    $rating->setValue($note);

    // Persistance de l'entité "Rating" modifiée
    $em = $this->getDoctrine()->getManager();
    $em->flush();

    // Redirection vers la page de détails du rating modifié

    return $this->render('rating/index.html.twig', [
        'rating' => $ratingRepository->findAll(),
    ]);
}*/
}