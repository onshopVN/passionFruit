<?php

namespace App\frontend\controller;

use App\skeleton\controller\InxController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends InxController
{
    /**
     * @Route("/", name="home_index")
     * @return Response
     */
    public function index()
    {
        return $this->render('frontend/index.twig', []);
    }

}