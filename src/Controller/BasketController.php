<?php

namespace App\Controller;

use Exception;
use Dompdf\Dompdf;
use App\Entity\Basket;
use DateTimeImmutable;
use App\Form\BasketType;
use App\Entity\CartProduct;
use App\Services\CartService;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Options;

#[Route('/basket')]
class BasketController extends AbstractController
{
    private $em;
    private $cartService;
    public function __construct(EntityManagerInterface $em, CartService $cartService) 
    {
        $this->em = $em;
        $this->cartService = $cartService;
    }


    #[Route('/', name: 'app_basket_show', methods: ['GET'])]
    public function show(): Response
    {
        $user = $this->getUser();
        $basket = $user->getCart();
        $cartproducts = $basket->getCartProducts();
        return $this->render('/FrontOffice/basket/show.html.twig', [
            'basket' => $basket,
            'cartproducts' => $cartproducts,
        ]);
    }

    #[Route('/{id}/editquantity', name: 'app_quantity_edit')]
    public function editQuantity(Request $request,$id): Response
    {
        $user = $this->getUser();
        $cart= $user->getCart();
        $cartproduct = $this->em->getRepository(CartProduct::class)->find($id);
        if ($request->isMethod("post")) 
        {
            $quantity=$request->get('quantity');
        }
        $cartproduct->setQuantity($quantity);
        $cartproduct->setTotal($cartproduct->getProduct()->getPrice()*$quantity);
        $cart->setTotal($this->cartService->TotalPriceCalcul($cart));
        $updatedAt = new DateTimeImmutable();
        $cart->setUpdatedAt($updatedAt);
        $this->em->flush();

        return $this->redirectToRoute('app_basket_show');
        
    }

    #[Route('/checkout', name: 'app_basket_checkout')]
    public function checkout(): Response
    {
        $user = $this->getUser();
        $cart= $user->getCart();
        if($cart->getTotal() <= $user->getPoints())
        {
            $cartproducts = $cart->getCartProducts();
            if(!empty($cartproducts))
            {
                foreach($cartproducts as $cartProduct)
                {
                    $product = $cartProduct->getProduct();
                    $product->setStock($product->getStock()-$cartProduct->getQuantity());
                    $user->setPoints($user->getPoints()-$cart->getTotal());
                    $cart->setCheckout("Done");
                    $this->em->flush();
                }
            }    //Render your HTML.twig page
            $invoicePDF = $this->renderView('/invoice.html.twig', [
                'basket' => $cart,
                'cartproducts' => $cartproducts,
            ]);

            // Instantiate Dompdf
            $pdf = new Dompdf();

            // Load HTML into Dompdf
            $pdf->loadHtml($invoicePDF);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            // Render the PDF
            $pdf->render();

            // Output the generated PDF to the browser
            $pdf->stream('invoice.pdf', ['Attachment' => true]);
            $pdfContent = $pdf->output();
            $response = new Response($pdfContent);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename="invoice.pdf"');
        
            return $response;

            // return $this->redirectToRoute('app_invoice');
        }
        else
        {
            return $this->redirectToRoute('app_invoice');
        }
        
    }

    #[Route('/invoice', name: 'app_invoice')]
    public function indexinvoice(): Response
    {
        $user = $this->getUser();
        $basket = $user->getCart();
        $cartproducts = $basket->getCartProducts();
        return $this->render('invoice.html.twig', [
            'basket' => $basket,
            'cartproducts' => $cartproducts,
        ]);
    }

}
