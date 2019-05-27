<?php

namespace App\language\entity;

use Doctrine\ORM\Mapping as ORM;


trait AuthenTrait
{
    /**
     * @ORM\Column(type="string", length=180)
     */
    protected $email_en;

    /**
     * @ORM\Column(type="string", length=180)
     */
    protected $email_vi;


}
