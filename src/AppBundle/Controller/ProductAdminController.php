<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductAdminController extends Controller
{
    /**
     * @Route("/admin/products", name="product_list")
     */
    public function listAction()
    {
        $products = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->findAll();

        return $this->render(
            'product/list.html.twig',
            [
                'products' => $products,
            ]
        );
    }

    /**
     * @Route("/admin/products/new", name="product_new")
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(new ProductType(), $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

//            $em = $this->getDoctrine()->getManager();
//            $em->persist($product);
//            $em->flush();

            $this->addFlash('success', 'Product created FTW!');

            $this->redirectToRoute('product_list');
        }

        return $this->render(
            'product/new.html.twig',
            ['form' => $form->createView()]
        );
    }
}
