<?php 
namespace App\Admin\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Core\Controller\ViewController;

class SecurityController extends ViewController
{
    /**
     * @Route("/admin/login", name="admin_login", methods="GET|POST")
     */
    public function login()
    {
        return $this->render('default/admin/security/login.twig');
    }

    /**
     * @Route("/admin/register", name="admin_register", methods="GET|POST")
     */
    public function register()
    {
        return $this->render('default/admin/security/register.twig');
    }
}
