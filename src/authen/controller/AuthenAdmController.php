<?php

namespace App\authen\controller;

use App\authen\repository\AuthenRepository;
use App\skeleton\controller\AdmController;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthenAdmController extends AdmController
{
    /**
     * AdminController constructor.
     * @param RequestStack $requestStack
     * @param AuthenRepository $objectRepository
     */
    public function __construct( AuthenRepository $objectRepository, SessionInterface $session)
    {
        parent::__construct($objectRepository, $session);
    }

    /**
     * @Route("/%route_admin%/authen/index", name="admin_authen_index")
     * @Template("@backend/authen/list.twig")
     * @return array
     */
    public function index(PaginatorInterface $paginator, SessionInterface $session): ?array
    {
        return parent::list($paginator, $session);
    }

    /**
     * @Route("/%route_admin%/authen/insert", name="admin_authen_insert")
     * @param Request $request
     * @Template("@backend/authen/form.twig")
     * @return array|null
     */

    public function insert()
    {
        return parent::create('admin_authen_index', []);
    }

    /**
     * @Route("/%route_admin%/authen/edit/{id}", name="admin_authen_edit")
     * @param Request $request
     * @Template("@backend/authen/form.twig")
     * @return array|null
     */
    public function edit(?int $id)
    {
        return parent::update($id, 'admin_authen_index', []);
    }

    /**
     * @Route("/%route_admin%/authen/delete/{id}",name="admin_authen_delete")
     * @param Request $request
     * @Template("@backend/authen/form.twig")
     * @return array|null
     */
    public function remove(?int $id)
    {
        return parent::delete($id, 'admin_authen_index', []);
    }

    /**
     * @Route("/%route_admin%/authen/show/{id}",name="admin_authen_show")
     * @Template("@backend/authen/view.twig")
     * @return array|null
     */
    public function show(?int $id): ?array
    {
        return parent::read($id);
    }
}