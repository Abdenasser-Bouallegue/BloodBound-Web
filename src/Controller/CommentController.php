<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Vote;
use App\Entity\Article;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt($comment->getCreatedAt());
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {   $article= $comment->getArticle();
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }

        return $this->redirectToRoute( 'app_article_show', ['id' =>$article->getId()]);
    }
    #[Route('/{id}/vote/{value}', name:"vote")]
    public function vote(Comment $comment, int $value): Response
    {
        $article= $comment->getArticle();
        // Check if the user has already voted on this article
        $vote = $this->getDoctrine()
            ->getRepository(Vote::class)
            ->findOneBy([
                'comment' => $comment,
                'user' => $this->getUser(),
            ]);

        if ($vote) {
            // User has already voted, update the value of their vote
            $vote->setValue($value);
        } else {
            // User hasn't voted yet, create a new vote
            $vote = new Vote();
            $vote->setComment($comment)
                ->setUser($this->getUser())
                ->setValue($value);
        }

        // Save the vote to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->redirectToRoute('app_article_showfront', ['id' =>$article->getId()]);
    }
}
