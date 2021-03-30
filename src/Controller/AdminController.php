<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddProductFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_homepage")
     */
    public function index(): Response
    {
        return $this->render('admin/products.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    /**
     * @Route("/admin/add/product", name="add_product")
     */
    public function addProduct(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(AddProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }
        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Add',
            'object' => 'product'
        ]);
    }
    /**
     * @Route("/admin/add/category", name="add_category")
     */
    public function addCategory()
    {
        return $this->render('admin/products.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
