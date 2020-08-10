<?php
namespace App\Auth\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Auth\Repository\LoginRepository")
 * @ORM\Table(name="auth_login")
 */
class Login implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     * @var string 
     */
    private $username = '';

    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     * @var string 
     */
    private $email = '';

    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     * @var string 
     */
    private $password = '';

    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     * @var string
     */
    private $salt = '';

    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     * @var string
     */
    private $roles = '';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $createdDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedDate;

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
        return $this;
    }

    /**
     * Get $username
     * @return string 
     */
    public function getUsername() : string 
    {
        return $this->username;
    }

    /**
     * Set $username
     * @param string $username
     * @return $this 
     */
    public function setUsername(string $username) : self 
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get $email
     * @return string 
     */
    public function getEmail() : string 
    {
        return $this->email;
    }

    /**
     * Set $email
     * @param string $email
     * @return $this 
     */
    public function setEmail(string $email) : self 
    {
        $this->email = $email;
        return $this;
    } 
    
    /**
     * Get $password
     * @return string 
     */
    public function getPassword() : string 
    {
        return $this->password;
    }

    /**
     * Set $password
     * @param string $password
     * @return $this 
     */
    public function setPassword(string $password) : self 
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get $salt
     * @return string 
     */
    public function getSalt() : string 
    {
        return $this->salt;
    }

    /**
     * Set $salt
     * @param string $salt
     * @return $this 
     */
    public function setSalt(string $salt) : self 
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Get $roles
     * @return string 
     */
    public function getRoles() : string 
    {
        return $this->roles;
    }

    /**
     * Set $roles
     * @param string $roles
     * @return $this 
     */
    public function setRoles(string $roles) : self 
    {
        $this->roles = $roles;
        return $this;
    }

        /**
     * Get $createdDate
     * @return \DateTime
     */
    public function getCreatedDate() : \DateTime
    {
        return $this->createdDate;
    }

    /**
     * Set $createdDate
     * @param \DateTime $createdDate
     * @return $this 
     */
    public function setCreatedDate(\DateTime $createdDate) : self 
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * Get $updatedDate
     * @return \DateTime
     */
    public function getUpdatedDate() : \DateTime
    {
        return $this->updatedDate;
    }

    /**
     * Set $updatedDate
     * @param \DateTime $updatedDate
     * @return $this 
     */
    public function setUpdatedDate(\DateTime $updatedDate) : self 
    {
        $this->updatedDate = $updatedDate;
        return $this;
    }  

    /**
     * @inheritdoc
     * @return void 
     */
    public function eraseCredentials()
    {
        
    }  
}
