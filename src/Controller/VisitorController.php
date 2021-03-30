<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Discount;
use App\Entity\DiscountProduct;
use App\Entity\Product;
use App\Form\CustomerFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class VisitorController extends AbstractController
{
    /**
     * @Route ("/products",name="products")
     */
    public function products(RequestStack $stack)
    {
        $query = $stack->getCurrentRequest()->query;
        $categoryId = $query->get('id');
        $r = $this->getDoctrine()->getRepository(Product::class);
        $products = $r->categoryProducts($categoryId);

        return $this->render('visitor/products.html.twig', [
            'products' => $products
        ]);
    }
    /**
     * @Route ("/", name="homepage")
     */
    public function categories()
    {
        $r = $this->getDoctrine()->getRepository(Category::class);
        $categories = $r->findAll();

        return $this->render('visitor/categories.html.twig', [
            'categories' => $categories
        ]);
    }
    /**
     * @Route ("/search", name="budget_search")
     */
    public function search()
    {

    }
    /**
     * @Route("/register", name="register")
     */
    public function makeAccount(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerFormType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $encodedPassword = $encoder->encodePassword($customer, $customer->getPassword());
            $customer->setPassword($encodedPassword);
            $customer->setRoles(["ROLE_CUSTOMER"]);
            $em->persist($customer);
            $em->flush();

            $this->addFlash('success', 'Account has been made!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('addCustomerForm.html.twig', [
            'form' => $form->createView(),
            'action' => 'Make',
            'object' => 'account'
        ]);
    }
}
