<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Row;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer/details/", name="account_details")
     */
    public function customerDetails()
    {
        $r = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $r->findOneBy(['id' => $this->getUser()->getId()]);
        return $this->render('customer/accountDetails.html.twig', [
            'customer' => $customer
        ]);
    }
    /**
     * @Route("/customer/orders/", name="customer_orders")
     */
    public function customerOrders()
    {
        $r = $this->getDoctrine()->getRepository(Order::class);
        $orders = $r->findBy(['customer' => $this->getUser()->getId()]);
        return $this->render('customer/orders.html.twig', [
            'orders' => $orders
        ]);
    }
}
//$r = $this->getDoctrine()->getRepository(Order::class);
//$order = $r->findOneBy(['id'=>$customerId]);
//foreach ($order->getRows2() as $row) {
//    dd($row);
//}
//dd($order->getRows2());