<?php
/**
 * Created by PhpStorm.
 * Profile: macos
 * Date: 2/15/19
 * Time: 3:08 PM
 */

namespace App\page\controller;

use App\page\entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/page/index", name="page_index")
     * @return Response
     */
    public function index()
    {
        return $this->render('page/index.twig', []);
    }
    /**
     * @Route("/page/insert", name="page_insert")
     */
    public function insert()
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();
        $page = new User();
        $page->setName('Keyboard'.rand(9,1999));
        $page->setPosition(rand(9,999));
        $page->setDescription('Ergonomic and stylish!'.rand(9,9999));
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($page);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        return new Response('Saved new page with id '.$page->getId());
    }
    /**
     * @Route("/page/{id}", name="page_show")
     */
    public function show($id)
    {
        $page = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if (!$page) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        return new Response('Check out this great product: '.$page->getName());
        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
    /**
     * @Route("/page/extra/{id}", name="page_show_extra")
     */
    public function showExtra(Page $page)
    {
        if (!$page) {
            throw $this->createNotFoundException(
                'No product found for id '.$page->getId()
            );
        }
        return new Response('Check out this great product: '.$page->getName());
        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
    /**
     * @Route("/page/edit/{id}")
     */
    public function update($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $page = $entityManager->getRepository(User::class)->find($id);
        if (!$page) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        $page->setName('New product name!'.rand(9, 100));
        $entityManager->flush();
        return $this->redirectToRoute('page_show', [
            'id' => $page->getId()
        ]);
    }
    /**
     * @Route("/page/delete/{id}")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $page = $entityManager->getRepository(User::class)->find($id);
        if (!$page) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        $entityManager->remove($page);
        $entityManager->flush();
        return $this->redirectToRoute('page', [
            'id' => $page->getId()
        ]);
    }
}