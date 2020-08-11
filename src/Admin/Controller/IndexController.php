<?php 
namespace App\Admin\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Core\Controller\ViewController;

class IndexController extends ViewController
{
    /**
     * @Route("/admin", name="admin_index", methods="GET")
     */
    public function index()
    {
        return $this->render('default/admin/index/index.twig');
    }
}
