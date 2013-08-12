<?php
namespace WScore\tests\Response\Mocks;

use WScore\Response\ResponsibleInterface;
use WScore\Response\ResponsibleTrait;

class Responsibility implements ResponsibleInterface
{
    use ResponsibleTrait;

    public $responded = false;

    public $name = null;

    public function Respond()
    {
        $this->responded = true;
        if( $this->name ) return $this->name;
        return null;
    }
}

