<?php

namespace App\language\entity;

use Doctrine\ORM\Mapping as ORM;


trait AuthenTrait
{
    /**
     * @ORM\Column(type="string", length=180, options={"translate":"email"})
     */
    protected $email_en;

    /**
     * @ORM\Column(type="string", length=180, options={"translate":"email"})
     */
    protected $email_vi;

    /**
     * @ORM\Column(type="string", length=255, options={"translate":"fullname"})
     */
    protected $fullname_en;

    /**
     * @ORM\Column(type="string", length=255, options={"translate":"fullname"})
     */
    protected $fullname_vi;

}
