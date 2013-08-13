<?php
namespace WScore\tests\Response\Mocks;

use WScore\Response\ModuleInterface;
use WScore\Response\ModuleTrait;

class Responsibility implements ModuleInterface
{
    use ModuleTrait;

    public $responded = false;

    public $name = null;

    public function Respond()
    {
        $this->responded = true;
        if( $this->name ) return $this->name;
        return null;
    }
}

