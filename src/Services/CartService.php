<?php

namespace App\Services;

use App\Entity\Basket;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CartProductRepository;
use Symfony\Component\HttpFoundation\Request;

class CartService
{
    public function __construct(
        private ProductRepository $productRepository,
        private CartProductRepository $cartProductRepository,
        private BasketRepository $cartRepository,
        private EntityManagerInterface $entityManager
    )
    {}

    public function TotalPriceCalcul(Basket $cart)
    {
        $total = 0;
        $cartProducts = $cart->getCartProducts();
        foreach($cartProducts as $cartProduct)
        {
            $total = $total + $cartProduct->getTotal();
        }
        return $total;
    }
}