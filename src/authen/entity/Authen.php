<?php

namespace App\authen\entity;

use App\deputation\entity\EntityInterface;
use App\deputation\entity\ProfileEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\authen\repository\AuthenRepository")
 */
class Authen implements EntityInterface, UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=180, unique=true, name="email", options={"lang":"multi"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email_en;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email_vi;


    /**
     * @ORM\Column(type="string", length=255, columnDefinition="")
     */
    private $username;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @var \App\deputation\entity\ProfileEntityInterface
     * @ORM\OneToOne(targetEntity="App\deputation\entity\ProfileEntityInterface")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     *})
     */
    private $profile;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return \App\deputation\entity\ProfileEntityInterface
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * get Profile
     * @param \App\deputation\entity\ProfileEntityInterface $profile
     * @return Authen
     */
    public function setProfile($profile = null): self
    {
        $this->profile = $profile;
        return $this;
    }

    public function getLanguage($field, $lang)
    {
        $new_field = $field.'_'.$lang;
        return $this->$new_field;
    }

    public function setLanguage($field, $lang, $value):self
    {
        $new_field = $field.'_'.$lang;

        $this->$new_field = $value;
        return $this;
    }
}
