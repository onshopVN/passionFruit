<?php

namespace App\profile\controller;

use App\deputation\utils\ResultUtilsInterface;
use App\skeleton\controller\AdmController;
use App\profile\repository\ProfileRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileAdmController extends AdmController
{
    /**
     * ProfileAdmController constructor.
     * @param RequestStack $requestStack
     * @param ProfileRepository $objectRepository
     * @param ResultUtilsInterface $result
     * @param SessionInterface $session
     */
    public function __construct(ProfileRepository $objectRepository, SessionInterface $session)
    {
        parent::__construct($objectRepository, $session);
    }

    /**
     * @Route("/%route_admin%/profile/index", name="admin_profile_index")
     * @Template("@backend/profile/listpaginator.twig")
     * @return array
     */
    public function index(PaginatorInterface $paginator): ?array
    {
        return parent::list($paginator);
    }

    /**
     * @Route("/%route_admin%/profile/insert", name="admin_profile_insert")
     * @param Request $request
     * @Template("@backend/profile/form.twig")
     * @return array|null
     */

    public function insert()
    {
        return parent::create('admin_profile_index', []);
    }

    /**
     * @Route("/%route_admin%/profile/edit/{id}", name="admin_profile_edit")
     * @param Request $request
     * @Template("@backend/profile/form.twig")
     * @return array|null
     */
    public function edit(?int $id)
    {
        return parent::update($id, 'admin_profile_index', []);
    }

    /**
     * @Route("/%route_admin%/profile/delete/{id}",name="admin_profile_delete")
     * @param Request $request
     * @Template("@backend/profile/form.twig")
     * @return array|null
     */
    public function remove(?int $id)
    {
        return parent::delete($id, 'admin_profile_index', []);
    }

    /**
     * @Route("/%route_admin%/profile/show/{id}",name="admin_profile_show")
     * @Template("@backend/profile/view.twig")
     * @return array|null
     */
    public function show(?int $id): ?array
    {
        return parent::read($id);
    }
}