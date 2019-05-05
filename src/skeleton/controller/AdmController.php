<?php

namespace App\skeleton\controller;

use App\deputation\controller\AdmControllerInterface;
use App\deputation\repository\RepositoryInterface;
use App\deputation\utils\ResultUtilsInterface;
use App\skeleton\repository\SkeletonRepository;
use App\skeleton\utils\ResultUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdmController extends AbstractController implements AdmControllerInterface
{
    protected $objectRepository;
    protected $requestStack;
    protected $session;

    /**
     * AdmController constructor.
     * @param SkeletonRepository $objectRepository
     * @param SessionInterface $session
     */
    public function __construct(SkeletonRepository $objectRepository, SessionInterface $session)
    {
        $this->objectRepository = $objectRepository;
        $this->session = $session;
    }

    public function getObjectRepository(): ?RepositoryInterface
    {
        return $this->objectRepository;
    }

    public function setObjectRepository(?RepositoryInterface $repository)
    {
        $this->objectRepository = $repository;
        return $this;
    }

    public function list($paginator): ?array
    {
        $this->denyAccessUnlessGranted('list', $this->objectRepository->newEntity());

        $request = $this->_getRequest();
        $session = $this->container->get('session');

        if ($session->get('form.search') && $request->get('page')) {
            $object = $session->get('form.search');
        } else {
            $object = $this->objectRepository->newEntity();
            $session->remove('form.search');
        }

        $form = $this->createForm($this->objectRepository->getFormClass(), $object, ['validation_groups' => ['list']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('form.search', $form->getData());
        }

        $queryBuilder = $this->objectRepository->queryBuilderWithObject($object);
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $this->params->get('pagination.items.limit.onpage')/*limit per page*/
        );

        return ['pagination' => $pagination, 'form' => $form->createView()];
    }

    public function create(?string $route, ?array $routeArray)
    {
        $this->denyAccessUnlessGranted('create', $this->objectRepository->newEntity());
        $object = $this->_object(null);
        $result = $this->_form($object, ['validation_groups' => ['create']]);
        if ($result->isSuccess()) {
            return $this->redirectToRoute($route, $routeArray);
        }
        return ['form' => $result->getObject()->createView()];
    }

    public function update(?int $id, ?string $route, ?array $routeArray)
    {
        $object = $this->_object($id);
        $this->denyAccessUnlessGranted('update', $object);
        $result = $this->_form($object, ['validation_groups' => ['update']]);
        if ($result->isSuccess()) {
            return $this->redirectToRoute($route, $routeArray);
        }
        return ['form' => $result->getObject()->createView()];
    }

    public function delete(?int $id, ?string $route, ?array $routeArray)
    {
        $object = $this->_object($id);
        $this->denyAccessUnlessGranted('delete', $object);
        $this->objectRepository->delete($object);
        return $this->redirectToRoute($route, $routeArray);
    }

    public function read(?int $id): ?array
    {
        $object = $this->_object($id);
        $this->denyAccessUnlessGranted('read', $object);
        return ['object' => $object];
    }

    protected function _object(?int $id)
    {
        if ($id) {
            $object = $this->objectRepository->find($id);
            if (!$object) {
                throw $this->createNotFoundException(
                    'No record found for id ' . $id
                );
            }
        } else {
            $object = $this->objectRepository->newEntity();
        }
        return $object;
    }

    protected function _form($object, $options = []): ?ResultUtilsInterface
    {
        $request = $this->_getRequest();
        $form = $this->createForm($this->objectRepository->getFormClass(), $object, $options);
        $form->handleRequest($request);
        $result = new ResultUtils();
        if ($form->isSubmitted() && $form->isValid()) {
            $this->objectRepository->save($object);

            $result->setSuccess();
            $result->setObject($form);
            return $result;
        }
        $result->setFail();
        $result->setObject($form);
        return $result;
    }

    /**
     * @throws \LogicException
     */
    protected function _getRequest(): Request
    {
        $this->requestStack = $this->container->get('request_stack');
        $this->params = $this->container->get('parameter_bag');

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new \LogicException('Request should exist so it can be processed for error.');
        }

        return $request;
    }

}