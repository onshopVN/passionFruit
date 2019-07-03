<?php

namespace App\skeleton\entity;


use App\deputation\entity\EntityInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class AbstractEntity implements EntityInterface
{

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