<?php

/**
 * CommentController class.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use App\Interface\CommentServiceInterface;
use App\Service\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\CommentVoter;

/**
 * Controller responsible for managing comments.
 *
 * This controller provides methods to create and display comments on posts.
 */
class CommentController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CommentServiceInterface $commentService the service for managing comments
     * @param PostService             $postService    the service for managing posts
     * @param TranslatorInterface     $translator     the translator service for translations
     */
    public function __construct(private readonly CommentServiceInterface $commentService, private readonly PostService $postService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Displays the form to add a comment to a post.
     *
     * @param int          $id      the ID of the post to which the comment will be added
     * @param Request      $request the HTTP request object
     * @param Comment|null $comment the comment entity, if it exists (optional)
     *
     * @return Response the response containing the rendered form
     */
    #[Route('/post/{id}/comment/add', name: 'comment_add', methods: ['GET', 'POST'])]
    #[IsGranted(CommentVoter::CREATE, subject: 'comment')]
    public function add(int $id, Request $request, ?Comment $comment): Response
    {
        $post = $this->postService->getPostById($id);

        $comment = new Comment();
        $comment->setPost($post);

        $user = $this->getUser();
        $comment->setEmail($user->getEmail());
        $comment->setNickname($user->getNickname());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->saveComment($comment);
            $this->addFlash('success', $this->translator->trans('message.%entity%.created_successfully', ['%entity%' => $this->translator->trans('entity.comment')]));

            return $this->redirectToRoute('post_show', ['id' => $id]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $post->getComments(),
            'comment_form' => $form->createView(),
        ]);
    }

    /**
     * Displays the form to edit an existing comment.
     *
     * @param int          $id      the ID of the comment to edit
     * @param Request      $request the HTTP request object
     * @param Comment|null $comment the comment entity, if it exists (optional)
     *
     * @return Response the response containing the rendered form or redirect
     */
    #[Route('/comment/{id}/edit', name: 'comment_edit', methods: 'GET|PUT')]
    #[IsGranted(CommentVoter::EDIT, subject: 'comment')]
    public function edit(int $id, Request $request, ?Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_edit', ['id' => $comment->getId()]),
            'method' => 'PUT',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->saveComment($comment);
            $this->addFlash('success', $this->translator->trans('message.%entity%.updated_successfully', ['%entity%' => $this->translator->trans('entity.comment')]));

            return $this->redirectToRoute('post_show', ['id' => $comment->getPost()->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }

    /**
     * Deletes a comment.
     *
     * @param int          $id      the ID of the comment to delete
     * @param Request      $request the HTTP request object
     * @param Comment|null $comment the comment entity, if it exists (optional)
     *
     * @return Response the response containing the rendered form or redirect
     */
    #[Route('/comment/{id}/delete', name: 'comment_delete', methods: 'GET|DELETE')]
    #[IsGranted(CommentVoter::DELETE, subject: 'comment')]
    public function delete(int $id, Request $request, ?Comment $comment): Response
    {
        $comment = $this->commentService->getCommentById($id);

        $postId = $comment->getPost()->getId();

        $formBuilder = $this->createFormBuilder(null, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->deleteComment($comment);
            $this->addFlash('success', $this->translator->trans('message.%entity%.deleted_successfully', ['%entity%' => $this->translator->trans('entity.comment')]));

            return $this->redirectToRoute('post_show', ['id' => $postId]);
        }

        return $this->render('comment/delete.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
            'postId' => $postId,
        ]);
    }
}
