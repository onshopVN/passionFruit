<?php 
namespace App\Core\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="core_index", methods="GET")
     */
    public function index(Request $request)
    {
        return $this->render('Core/index.twig');
    }
}
