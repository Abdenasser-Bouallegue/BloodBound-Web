<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/product/category')]
class ProductCategoryController extends AbstractController
{
    private $em;
    private $productCategoryRepository;
    public function __construct(EntityManagerInterface $em, ProductCategoryRepository $productCategoryRepository) 
    {
        $this->em = $em;
        $this->productCategoryRepository = $productCategoryRepository;
    }
    #[Route('/', name: 'app_product_category_index', methods: ['GET'])]
    public function index(ProductCategoryRepository $productCategoryRepository): Response
    {
        return $this->render('BackOffice/product_category/index.html.twig', [
            'product_categories' => $productCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $productCategory = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($productCategory);
            $this->em->flush();

            return $this->redirectToRoute('app_product_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BackOffice/product_category/new.html.twig', [
            'product_category' => $productCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_category_show')]
    public function show($id): Response
    {
        return $this->render('BackOffice/product_category/show.html.twig', [
            'product_category' => $this->productCategoryRepository->find($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id): Response
    {
        $productCategory = $this->productCategoryRepository->find($id);
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('app_product_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BackOffice/product_category/edit.html.twig', [
            'product_category' => $productCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_category_delete', methods: ['POST'])]
    public function delete($id): Response
    {

        $productCategory = $this->productCategoryRepository->find($id);
        $this->em->remove($productCategory);
        $this->em->flush();
        

        return $this->redirectToRoute('app_product_category_index');
    }
}

