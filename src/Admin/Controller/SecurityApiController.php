<?php 
namespace App\Admin\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Core\Controller\ServiceController;
use App\Admin\Repository\AdminRepository;
use App\Skeleton\Repository\AuthLoginRI;

class SecurityApiController extends ServiceController
{
    /**
     * @var AdminRepository
     */
    private $adminR;

    /**
     * @var AuthLoginRI
     */
    private $authLoginR;

    /**
     * @param AdminRepository $adminR
     * @param AuthLoginRI $authLoginR
     */
    public function __construct(
        AdminRepository $adminR,
        AuthLoginRI $authLoginR
    ) {
        $this->adminR = $adminR;    
        $this->authLoginR = $authLoginR;
    }

    /**
     * @Route("/admin/api/register", name="admin_api_security_register", methods="POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $response = [];
        
        try {
            $admin = $this->adminR->newEntity();
            $admin = $this->adminR->copyValues($admin, $request->get('admin'));
            $errors = $this->validator->validate($admin);
            if (count($errors) > 0) {
                throw new \Exception();
            }

            $this->adminR->save($admin);


            $response['success'] = 'Action success!';
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
        }

        return $this->json($response);
    }
}
