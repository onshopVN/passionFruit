<?php

namespace App\backend\controller;

use App\skeleton\controller\AdmController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;

class HomeAdmController extends AdmController
{
    /**
     * @Route("/%route_admin%/", name="admin_home")
     * @return Response
     */
    public function index()
    {
//        $this->denyAccessUnlessGranted('view', $role);
        return $this->render('@backend/home.twig', []);
    }

}