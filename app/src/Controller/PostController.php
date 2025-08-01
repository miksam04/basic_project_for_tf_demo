<?php

/**
 * Home controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use App\Interface\PostServiceInterface;
use App\Interface\CategoryServiceInterface;
use App\Interface\ImageServiceInterface;
use App\Service\CommentService;
use App\Service\TagService;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Image;
use App\Form\Type\CommentType;
use App\Form\Type\PostType;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Dto\PostListInputFiltersDto;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\PostVoter;

/**
 * Controller responsible for managing posts and and filtering them.
 */
class PostController extends AbstractController
{
    public $imageService;
    public $tagService;
    public $commentService;
    public $postService;
    public $categoryService;
    public $translator;

    /**
     * Constructor.
     *
     * @param PostServiceInterface     $postService     the post service
     * @param CategoryServiceInterface $categoryService the category service
     * @param TranslatorInterface      $translator      the translator service
     * @param CommentService           $commentService  the comment service
     * @param TagService               $tagService      the tag service
     * @param ImageServiceInterface    $imageService    the image service
     */
    public function __construct(PostServiceInterface $postService, CategoryServiceInterface $categoryService, TranslatorInterface $translator, CommentService $commentService, TagService $tagService, ImageServiceInterface $imageService)
    {
        $this->postService = $postService;
        $this->categoryService = $categoryService;
        $this->commentService = $commentService;
        $this->tagService = $tagService;
        $this->translator = $translator;
        $this->imageService = $imageService;
    }

    /**
     * Displays the list of posts.
     *
     * @param PostListInputFiltersDto $filters the filters for the post list
     * @param int                     $page    the page number
     *
     * @return Response the response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/', name: 'post_index', methods: 'GET')]
    public function index(#[MapQueryString(resolver: PostListInputFiltersDtoResolver::class)] PostListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $user = $this->getUser();

        $pagination = $this->postService->getPaginatedPosts($page, $user, $filters);

        return $this->render('home/index.html.twig', [
            'posts' => $pagination,
            'categories' => $this->categoryService->getAllCategories(),
        ]);
    }

    /**
     * Displays a single post.
     *
     * @param int $id the post ID
     *
     * @return Response the response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/post/{id}', requirements: ['id' => '[1-9]\d*'], name: 'post_show')]
    public function showPost(int $id): Response
    {
        $post = $this->postService->getPostById($id);
        $comments = $this->commentService->getCommentsByPost($post);

        $comment = new Comment();
        $comment->setPost($post);
        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'comment' => $comment,
            'comment_form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new post.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/post/create', name: 'post_create')]
    #[IsGranted(PostVoter::CREATE)]
    public function createPost(Request $request): Response
    {
        $user = $this->getUser();
        $post = new Post();
        $post->setAuthor($user);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('images')->getData();
            foreach ($files as $file) {
                $this->imageService->create($file, $post);
            }
            $this->postService->savePost($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.%entity%.created_successfully', ['%entity%' => $this->translator->trans('entity.post')])
            );

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $this->categoryService->getAllCategories(),
        ]);
    }

    #[IsGranted(PostVoter::EDIT, subject: 'post')]
    #[\Symfony\Component\Routing\Attribute\Route('/post/{id}/edit', requirements: ['id' => '[1-9]\d*'], name: 'post_edit', methods: 'GET|PUT')]

    /**
     * Displays the form to edit an existing post.
     *
     * @param Request $request the request object
     * @param Post    $post    the post entity
     *
     * @return Response the response object
     */
    public function editPost(Request $request, Post $post): Response
    {
        $defaultReturnUrl = $this->generateUrl('post_index');

        $returnToUrl = $request->query->get('returnTo', $defaultReturnUrl);


        $form = $this->createForm(
            PostType::class,
            $post,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('post_edit', ['id' => $post->getId(), 'returnTo' => $returnToUrl]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagesToDelete = $request->request->all('delete_images');
            if ($imagesToDelete) {
                foreach ($post->getImages() as $image) {
                    if (in_array($image->getId(), $imagesToDelete)) {
                        $this->imageService->removeFile($image);
                        $post->removeImage($image);
                    }
                }
            }

            $files = $form->get('images')->getData();
            foreach ($files as $file) {
                $this->imageService->create($file, $post);
            }
            $this->postService->savePost($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.%entity%.updated_successfully', ['%entity%' => $this->translator->trans('entity.post')])
            );

            return $this->redirectToRoute('post_show', ['id' => $post->getId(), 'returnTo' => $returnToUrl]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'categories' => $this->categoryService->getAllCategories(),
            'cancel_url' => $returnToUrl,
        ]);
    }

    /**
     * Deletes a post.
     *
     * @param Request $request the request object
     * @param Post    $post    the post entity
     * @param int     $page    the page number
     *
     * @return Response the response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/post/{id}/delete', requirements: ['id' => '[1-9]\d*'], name: 'post_delete', methods: 'GET|DELETE')]
    #[IsGranted(PostVoter::DELETE, subject: 'post')]
    public function deletePost(Request $request, Post $post, #[MapQueryParameter] int $page = 1): Response
    {
        $defaultReturnUrl = $this->generateUrl('post_index');

        $returnToUrl = $request->query->get('returnTo', $defaultReturnUrl);

        $formBuilder = $this->createFormBuilder(null, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('post_delete', ['id' => $post->getId(), 'returnTo' => $returnToUrl]),
        ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($post->getImages() as $image) {
                $this->imageService->removeFile($image);
            }
            $this->postService->deletePost($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.%entity%.deleted_successfully', ['%entity%' => $this->translator->trans('entity.post')])
            );

            return $this->redirectToRoute('post_index');
        }



        return $this->render('post/delete.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'cancel_url' => $returnToUrl,
        ]);
    }
}
