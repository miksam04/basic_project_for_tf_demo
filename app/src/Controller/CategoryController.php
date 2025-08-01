<?php

/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use App\Interface\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller responsible for managing categories.
 */
class CategoryController extends AbstractController
{
    public $categoryService;
    public $translator;

    /**
     * Constructor.
     *
     * @param CategoryServiceInterface $categoryService the category service
     * @param TranslatorInterface      $translator      the translator service
     */
    public function __construct(CategoryServiceInterface $categoryService, TranslatorInterface $translator)
    {
        $this->categoryService = $categoryService;
        $this->translator = $translator;
    }

    /**
     * Displays the list of categories.
     *
     * @param int $page The page number
     *
     * @return Response the response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/categories', name: 'category_index')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $pagination = $this->categoryService->getPaginatedCategories($page);

        return $this->render('category/index.html.twig', [
            'categories' => $pagination,
        ]);
    }

    /**
     * Displays the form to create a new category.
     *
     * @param Request $request The request object
     * @param int     $page    The page number
     *
     * @return Response the response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/categories/create', name: 'category_create')]
    public function create(Request $request, #[MapQueryParameter] int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        $pagination = $this->categoryService->getPaginatedCategories($page);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.%entity%.created_successfully', ['%entity%' => $this->translator->trans('entity.category')])
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $pagination,
        ]);
    }

    /**
     * Displays the form to edit an existing category.
     *
     * @param Request  $request  The request object
     * @param Category $category The category entity
     * @param int      $page     The page number
     *
     * @return Response the response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/categories/{id}/edit', name: 'category_edit', methods : 'GET|PUT')]
    public function edit(Request $request, Category $category, #[MapQueryParameter] int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(
            CategoryType::class,
            $category,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('category_edit', ['id' => $category->getId()]),
            ]
        );
        $form->handleRequest($request);

        $pagination = $this->categoryService->getPaginatedCategories($page);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.%entity%.updated_successfully', ['%entity%' => $this->translator->trans('entity.category')])
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'categories' => $pagination,
        ]);
    }

    /**
     * Displays the form to delete an existing category.
     *
     * @param Request  $request  The request object
     * @param Category $category The category entity
     * @param int      $page     The page number
     *
     * @return Response the response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/categories/{id}/delete', name: 'category_delete', methods : 'GET|DELETE')]
    public function delete(Request $request, Category $category, #[MapQueryParameter] int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$this->categoryService->canBeDeleted($category)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.%entity%.cannot_be_deleted', ['%entity%' => $this->translator->trans('entity.category')])
            );

            return $this->redirectToRoute('category_index');
        }
        $formBuilder = $this->createFormBuilder(null, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('category_delete', ['id' => $category->getId()]),
        ]);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        $pagination = $this->categoryService->getPaginatedCategories($page);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.%entity%.deleted_successfully', ['%entity%' => $this->translator->trans('entity.category')])
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/delete.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'categories' => $pagination,
        ]);
    }
}
