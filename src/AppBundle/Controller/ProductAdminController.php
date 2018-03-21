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

        $form = $this->createForm(
            new ProductType(),
            $product,
            ['action' => $this->generateUrl('product_new')]
        );


        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));


            if ($form->isSubmitted() && $form->isValid()) {
                $product = $form->getData();
                $product->setAuthor($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                $this->addFlash('success', 'Product created FTW!');

                return $this->redirectToRoute('product_list');
            }
        }

        return $this->render(
            'product/new.html.twig',
            ['form' => $form->createView()]
        );

    }

    /**
     * @Route("/admin/products/delete/{id}", name="product_delete")
     */
    public function deleteAction(Product $product, Request $request)
    {
        if ($request->isMethod('DELETE')) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'The product was deleted');

            return $this->redirectToRoute('product_list');
        }
    }
}
