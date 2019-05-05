<?php

namespace App\authen\controller;

use App\authen\repository\AuthenRepository;
use App\deputation\services\AuthenServiceInterface;
use App\deputation\utils\ResultUtilsInterface;
use App\skeleton\controller\AdmController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AdmController
{

    /**
     * LoginController constructor.
     * @param AuthenServiceInterface $authen
     */
    public function __construct(AuthenServiceInterface $authen)
    {
        $this->authen = $authen;
    }

    /**
     * @Route("/authen/login", name="authen_login")
     * @Template("@backend/authen/login.twig")
     * @return Response
     */
    public function index(AuthenticationUtils $authenticationUtils): array
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return [
            'last_username' => $lastUsername,
            'error' => $error
        ];

    }

    /**
     * @Route("/authen/logout", name="authen_logout")
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');

    }


}