<?php

namespace App\profile\entity;

use App\deputation\entity\EntityInterface;
use App\skeleton\entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\deputation\entity\AuthenEntityInterface;

/**
 * @ORM\Entity(repositoryClass="App\profile\repository\ProfileRepository")
 */
class Profile extends AbstractEntity //implements EntityInterface
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gender;
    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $description;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $contact;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $company;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $locale;

    /**
     * @var \App\deputation\entity\AuthenEntityInterface
     * @ORM\OneToOne(targetEntity="App\deputation\entity\AuthenEntityInterface")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="authen_id", referencedColumnName="id")
     *})
     */
    private $authen;


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }
    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return \App\deputation\entity\AuthenEntityInterface
     */
    public function getAuthen()
    {
        return $this->authen;
    }

    /**
     * get Authen
     * @param \App\deputation\entity\AuthenEntityInterface|null $authen
     * @return Profile
     */
    public function setProfile(AuthenEntityInterface $authen = null): self
    {
        $this->authen = $authen;
        return $this;
    }
}