<?php 
namespace App\Auth\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Core\Controller\ServiceController;

class SecurityController extends ServiceController
{
    /**
     * @Route("/login", name="login", methods="POST")
     */
    public function login()
    {
        // nothing here
    }

    /**
     * @Route("/logout", name="logout", methods="GET|POST")
     */
    public function logout()
    {
        // nothing here
    }
}
