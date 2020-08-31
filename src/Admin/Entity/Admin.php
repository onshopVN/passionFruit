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

    /**
     * Get $id
     * @return int 
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Set $id
     * @param int $id
     * @return $this
     */
    public function setId(int $id) : self 
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get $fullname
     * @return string 
     */
    public function getFullname() : string 
    {
        return $this->fullname;
    }

    /**
     * Set $fullname
     * @param string $fullname
     * @return $this
     */
    public function setFullname(string $fullname) : self 
    {
        $this->fullname = $fullname;
        return $this;
    }

    /**
     * Get $loginId
     * @return int 
     */
    public function getLoginId() : ?int
    {
        return $this->loginId;
    }

    /**
     * Set $loginId
     * @param int $loginId
     * @return $this
     */
    public function setLoginId(int $loginId) : self 
    {
        $this->loginId = $loginId;
        return $this;
    }    
}
