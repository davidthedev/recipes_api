<?php

namespace App\Controllers;

abstract class BaseController
{
    protected $container;
    protected $data;
    protected $v;

    public function __construct($container)
    {
        $this->container = $container;
        $this->data = $this->Data;
    }

    /**
     * Easy data access
     *
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->container->{$property};
    }
}
