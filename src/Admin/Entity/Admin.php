<?php 
namespace App\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Admin\Repository\AdminRepository")
 * @ORM\Table(name="admin_admin")
 */
class Admin 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     */
    private $fullname = '';

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    private $loginId = 0;
}
