<?php

namespace App\Controller;

use App\Entity\Discount;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Row;
use App\Form\DiscountFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/customer/cart/{productId}", name="add_to_cart")
     */
    public function addToCart($productId)
    {
        $session = new Session();
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['id'=>$productId]);
        $inCart = false;

        if($session->has('cart'))
        {
            $cart=$session->get('cart');
        }else
        {
            $session->set('cart',['products' =>[]]);
            $cart = $session->get('cart');
        }
        $toBeBought=
            [
                'id'=>$productId,
                'price' => $product->getPrice(),
                'amount'=> 1,
            ];
        foreach ($cart['products'] as $key=>$product)
        {
            if($toBeBought['id']==$product['id'])
            {
                $cart['products'][$key]['amount'] ++;
                $inCart = true;
            }
        }
        if (!$inCart)
        {
            array_push($cart['products'], $toBeBought);
        }
        $session ->set('cart',$cart);
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route ("/customer/view/cart", name="cart")
     */
    public function cart(Request $request) {
        $session = $request->getSession();
        $r = $this->getDoctrine()->getRepository(Product::class);

        $productIds = $session->get('cart');
        $products = [];

        $form = $this->createForm(DiscountFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $cart = $session->get('cart');


            foreach ($productIds['products'] as $key=>$productId) {

                $product = $r->findOneBy(['id' => $productId['id']]);

                foreach ($product->getDiscountProducts() as $discount) {

                    if($discount->getDiscount()->getCode() == $data['code']) {
                        $discountedPrice = $cart['products'][$key]['price']/100*(100-$discount->getDiscount()->getPercentage());
                        $discountedPrice = round($discountedPrice, 2);
                        $cart['products'][$key]['price'] = $discountedPrice;

                        $session->set('cart', $cart);
                        $session->set('discount', $discount->getDiscount());

                        $this->addFlash('success', 'It worked, hopefully');
                        return $this->redirectToRoute('cart');
                    }
                }
            }
            $this->addFlash('error', 'Code did not work');
            return $this->redirectToRoute('cart');
        }

        if ($productIds) {
            foreach ($productIds['products'] as $product) {
                $pro = $r->findOneBy(['id' => $product['id']]);
                array_push($products, $pro);
            }
        }

        return $this->render('cart/cart.html.twig',[
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/customer/process", name="process")
     */
    public function process(Request $request) {
        $session = $request->getSession();
        $r = $this->getDoctrine()->getRepository(Product::class);

        $em = $this->getDoctrine()->getManager();


        $order = new Order();
        $order->setDate(new \DateTime());
        $order->setCustomer($this->getUser());
        $order->setDiscountUsed(0);
        $order->setDiscountCode(null);
        $em->persist($order);

        foreach ($session->get('cart')['products'] as $prod) {
            $product = $r->findOneBy(['id' => $prod['id']]);

            $row = new Row();
            $row->setAmount($prod['amount']);
            $row->setTheOrder($order);
            $row->setProduct($product);
            $em->persist($row);
        }
        $em->flush();
        return $this->redirectToRoute('add_total',['orderId'=>$order->getId()]);
    }
    /**
     * @Route ("/customer/empty/cart", name="empty_cart")
     */
    public function emptyCart(Request $request) {
        $session = $request->getSession();
        $session->remove('cart');
        $session->remove('total');
        $session->remove('discount');
        return $this->redirectToRoute('cart');
    }

    //adds the total to the order
    /**
     * @Route ("/customer/add/total", name="add_total")
     */
    public function addTotal(RequestStack $stack, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $query = $stack->getCurrentRequest()->query;
        $orderId = $query->get('orderId');

        $r = $this->getDoctrine()->getRepository(Order::class);

        $order = $r->findOneBy(['id' => $orderId]);

        $order->setTotal($session->get('total'));
        if ($session->has('discount')) {
            $re = $this->getDoctrine()->getRepository(Discount::class);
            $discount = $re->findOneBy(['id' => $session->get('discount')->getId()]);
            $order->setDiscountCode($discount);
            $order->setDiscountUsed(1);
        }

        $em->persist($order);
        $em->flush();

        $session->remove('cart');
        $session->remove('total');
        $session->remove('discount');

        $this->addFlash('success', 'Order has been made');
        return $this->redirectToRoute('customer_orders');
    }
}
