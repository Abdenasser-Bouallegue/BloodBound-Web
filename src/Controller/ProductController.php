<?php

namespace App\Controller;

use COM;
use App\Entity\User;
use App\Entity\Basket;
use DateTimeImmutable;
use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\CartProduct;
use App\Entity\ScratchCode;
use App\Services\CartService;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



#[Route('/product')]
class ProductController extends AbstractController
{
    private $em;
    private $productRepository;
    private $cartService;
    public function __construct(EntityManagerInterface $em, ProductRepository $productRepository, CartService $cartService) 
    {
        $this->em = $em;
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function indexFront(Request $request,ProductRepository $productRepository): Response
    {
        
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $numberOfProductsPerPage = 3;
        $totalProducts = count($products);
        $totalPages = ceil($totalProducts / $numberOfProductsPerPage);
        $pageNumber = $request->query->getInt('page', 1);
        $offset = ($pageNumber - 1) * $numberOfProductsPerPage;
        $limit = $numberOfProductsPerPage;
        $productsOnCurrentPage = array_slice($products, $offset, $limit);

        return $this->render('FrontOffice/product/index.html.twig', [
            'products' => $productsOnCurrentPage,
            'totalPages' => $totalPages,
            'currentPage' => $pageNumber,
        ]);

        
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'app_product_index_admin', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
    
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $numberOfProductsPerPage = 2;
        $totalProducts = count($products);
        $totalPages = ceil($totalProducts / $numberOfProductsPerPage);
        $pageNumber = $request->query->getInt('page', 1);
        $offset = ($pageNumber - 1) * $numberOfProductsPerPage;
        $limit = $numberOfProductsPerPage;
        $productsOnCurrentPage = array_slice($products, $offset, $limit);


        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('c.name AS category', 'COUNT(p.id) AS count')
             ->from('App\Entity\Product', 'p')
             ->join('p.productCategory', 'c')
             ->groupBy('c.name');

        $data = $queryBuilder->getQuery()->getResult();

        // Using array_map to extract category names and counts
        $labels = array_map(function ($row) {
    return $row['category'];
    }, $data);

    $values = array_map(function ($row) {
    return $row['count'];
    }, $data);

    $chartData = [
    'labels' => $labels,
    'values' => $values,
    ];
        

        return $this->render('BackOffice/product/index.html.twig', [
            'products' => $productsOnCurrentPage,
            'totalPages' => $totalPages,
            'currentPage' => $pageNumber,
            'chartData' => $chartData,
        ]);

        
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            //upload img
            $image = $form->get('image')->getData();

            // this condition is needed because the 'image' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $image->move(
                        $this->getParameter('img_directory'),
                        $newFilename
                    );

                    
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'imageename' property to store img name
                // instead of its contents
                $product->setImage($newFilename);
                
            }

            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('app_product_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BackOffice/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);

    }

    #[Route('/admin/{id}', name: 'app_product_show_admin', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Product $product): Response
    {
        return $this->render('BackOffice/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show')]
    public function showFront($id): Response
    {
        $product = $this->productRepository->find($id);
        return $this->render('FrontOffice/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/addtocart', name: 'app_product_cart')]
    public function addToCart($id): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();


        $product = $this->productRepository->find($id);
        $cartProducts = $cart->getCartProducts();
        $found = false;

        if(!empty($cartProducts))
        {
            foreach($cartProducts as $cartProduct)
            {
                if($product->getId() == $cartProduct->getProduct()->getId())
                {
                    if($product->getStock() > 0)
                    {
                        $cartProduct->setQuantity($cartProduct->getQuantity()+1);
                        $cartProduct->setTotal($cartProduct->getTotal()+$product->getPrice());
                        $found = true;
                    }
                    break;
                }
            }
        }

        if($found == false){
            if($product->getStock() > 0)
            {
                $cartProduct = new CartProduct();
                $cartProduct->setProduct($product);
                $cartProduct->setCart($cart);
                $cartProduct->setQuantity(1);
                $cartProduct->setTotal($product->getPrice());
                $this->em->persist($cartProduct);
                $this->em->flush();
            }
        }
        $cart->setTotal($this->cartService->TotalPriceCalcul($cart));
        $updatedAt = new DateTimeImmutable();
        $cart->setUpdatedAt($updatedAt);
        $this->em->flush();

        return $this->redirectToRoute('app_product_index');
    }

    #[Route('/{id}/addquantity', name: 'app_product_cart_quantity')]
    public function addToCartByQuantity(Request $request,$id): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        if ($request->isMethod("post")) {
            $quantity=$request->get('quantity');
        }

        $product = $this->productRepository->find($id);
        $cartProducts = $cart->getCartProducts();
        $found = false;

        if(!empty($cartProducts))
        {
            foreach($cartProducts as $cartProduct)
            {
                if($product->getId() == $cartProduct->getProduct()->getId())
                {
                    if($product->getStock() >= $quantity)
                    {
                        $cartProduct->setQuantity($cartProduct->getQuantity()+$quantity);
                        $cartProduct->setTotal($cartProduct->getTotal()+($product->getPrice()*$quantity));
                        $found = true;
                    }
                    break;
                }
            }
        }

        if($found == false)
        {
            if($product->getStock() >= $quantity)
            {
                $cartProduct = new CartProduct();
                $cartProduct->setProduct($product);
                $cartProduct->setCart($cart);
                $cartProduct->setQuantity($quantity);
                $product->setStock($product->getStock()-$quantity);
                $cartProduct->setTotal($product->getPrice()*$quantity);
                $this->em->persist($cartProduct);
            }
        }
        $cart->setTotal($this->cartService->TotalPriceCalcul($cart));
        $updatedAt = new DateTimeImmutable();
        $cart->setUpdatedAt($updatedAt);
        $this->em->flush();

        return $this->redirectToRoute('app_product_index');
    }

    #[Route('/{id}/buynow', name: 'app_product_buy')]
    public function buyNow($id): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();


        $product = $this->productRepository->find($id);
        $cartProducts = $cart->getCartProducts();
        $found = false;

        if(!empty($cartProducts))
        {
            foreach($cartProducts as $cartProduct)
            {
                if($product->getId() == $cartProduct->getProduct()->getId())
                {
                    if($product->getStock() > 0)
                    {
                        $cartProduct->setQuantity($cartProduct->getQuantity()+1);
                        $cartProduct->setTotal($cartProduct->getTotal()+$product->getPrice());
                        $found = true;
                    }
                    break;
                }
            }
        }

        if($found == false){
            if($product->getStock() > 0)
            {
                $cartProduct = new CartProduct();
                $cartProduct->setProduct($product);
                $cartProduct->setCart($cart);
                $cartProduct->setQuantity(1);
                $cartProduct->setTotal($product->getPrice());
                $this->em->persist($cartProduct);
                $this->em->flush();
            }
        }
        $cart->setTotal($this->cartService->TotalPriceCalcul($cart));
        $updatedAt = new DateTimeImmutable();
        $cart->setUpdatedAt($updatedAt);
        $this->em->flush();

        return $this->redirectToRoute('app_basket_show');
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository, $id , SluggerInterface $slugger): Response
    {
        $product = $this->productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        //upload img
                        $image = $form->get('image')->getData();

                        // this condition is needed because the 'image' field is not required
                        // so the PDF file must be processed only when a file is uploaded
                        if ($image) {
                            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                            // this is needed to safely include the file name as part of the URL
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
            
                            // Move the file to the directory where images are stored
                            try {
                                $image->move(
                                    $this->getParameter('img_directory'),
                                    $newFilename
                                );
            
                                
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                            }
            
                            // updates the 'imageename' property to store img name
                            // instead of its contents
                            $product->setImage($newFilename);
                            
                        }
            
            $this->em->flush();

            return $this->redirectToRoute('app_product_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BackOffice/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }
        return $this->redirectToRoute('app_product_index_admin', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/redeem', name: 'app_points_redeem')]
    public function redeem(Request $request): Response
    {
        $user = new User($this->getUser());
        $code = $request->query->get('scratch_code');
        $scratchCode = $this->em->getRepository(ScratchCode::class)->findByCode($code);
        if($scratchCode!=null)
            {
                $user->setPoints($user->getPoints()+$scratchCode->getPoints());
                $this->em->getRepository(ScratchCode::class)->remove($scratchCode, true);
                $this->em->flush();
            }
        return $this->redirectToRoute('app_product_index_admin', [], Response::HTTP_SEE_OTHER);
    }
}

