<?php

/**
 * TagController class.
 */

namespace App\Controller;

use App\Entity\Tag;
use App\Form\Type\TagType;
use App\Interface\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

/**
 * Controller responsible for managing tags.
 */
class TagController extends AbstractController
{
    public $tagService;
    public $translator;

    /**
     * TagController constructor.
     *
     * @param TagServiceInterface $tagService The tag service
     * @param TranslatorInterface $translator The translator service
     */
    public function __construct(TagServiceInterface $tagService, TranslatorInterface $translator)
    {
        $this->tagService = $tagService;
        $this->translator = $translator;
    }

    /**
     * Displays the form to create a new tag.
     *
     * @param int $page The page number
     *
     * @return Response the response object
     */
    #[Route('/tags', name: 'tag_index')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $pagination = $this->tagService->getPaginatedTags($page);

        return $this->render('tag/index.html.twig', [
            'tags' => $pagination,
        ]);
    }

    /**
     * Displays the form to edit an existing tag.
     *
     * @param Request $request The request object
     * @param Tag     $tag     The tag to edit
     * @param int     $page    The page number
     *
     * @return Response the response object
     */
    #[Route('/tags/{id}/edit', name: 'tag_edit', methods: 'GET|PUT')]
    public function edit(Request $request, Tag $tag, #[MapQueryParameter] int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $pagination = $this->tagService->getPaginatedTags($page);

        $form = $this->createForm(
            TagType::class,
            $tag,
            [
                'action' => $this->generateUrl('tag_edit', ['id' => $tag->getId()]),
                'method' => 'PUT',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);

            $this->addFlash('success', $this->translator->trans('message.%entity%.updated_successfully', ['%entity%' => $this->translator->trans('entity.tag')]));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/edit.html.twig', [
            'form' => $form->createView(),
            'tags' => $pagination,
            'tag' => $tag,
        ]);
    }

    /**
     * Displays the form to delete an existing tag.
     *
     * @param Request $request The request object
     * @param Tag     $tag     The tag to delete
     * @param int     $page    The page number
     *
     * @return Response the response object
     */
    #[Route('/tags/{id}/delete', name: 'tag_delete', methods: 'GET|DELETE')]
    public function delete(Request $request, Tag $tag, #[MapQueryParameter] int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$this->tagService->canBeDeleted($tag)) {
            $this->addFlash('warning', $this->translator->trans('message.%entity%.cannot_be_deleted', ['%entity%' => $this->translator->trans('entity.tag')]));

            return $this->redirectToRoute('tag_index', ['page' => $page]);
        }

        $formBuilder = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('tag_delete', ['id' => $tag->getId()]),
            'method' => 'DELETE',
        ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        $pagination = $this->tagService->getPaginatedTags($page);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->delete($tag);

            $this->addFlash('success', $this->translator->trans('message.%entity%.deleted_successfully', ['%entity%' => $this->translator->trans('entity.tag')]));

            return $this->redirectToRoute('tag_index', ['page' => $page]);
        }

        return $this->render('tag/delete.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
            'tags' => $pagination,
        ]);
    }
}
