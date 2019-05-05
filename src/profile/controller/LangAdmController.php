<?php

namespace App\profile\controller;

use App\deputation\utils\ResultUtilsInterface;
use App\skeleton\controller\AdmController;
use App\profile\repository\ProfileRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LangAdmController extends AdmController
{
    /**
     * AdminController constructor.
     * @param RequestStack $requestStack
     * @param ProfileRepository $objectRepository
     * @param ResultUtilsInterface $result
     */
    public function __construct(RequestStack $requestStack, ProfileRepository $objectRepository, ResultUtilsInterface $result)
    {
        $this->requestStack = $requestStack;
        $this->objectRepository = $objectRepository;
        $this->result = $result;
    }

    /**
     * @Route("/%route_admin%/profile/locale/{locale}", name="admin_profile_locale")
     * @return RedirectResponse
     */
    public function locale(?string $locale, SessionInterface $session)
    {
        $session->set('_locale', $locale);
        return $this->redirectToRoute('admin_home');
    }

}