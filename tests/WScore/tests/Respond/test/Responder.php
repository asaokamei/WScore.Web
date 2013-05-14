<?php
namespace WScore\tests\Respond\test;

use WScore\Web\Respond\RespondAbstract;

class Responder extends RespondAbstract
{

    public $name = 'responder';
    
    public $responded = false;
    
    /**
     * responds to a request.
     * returns null if there is no response.
     *
     * @param array $match
     * @return $this|null
     */
    public function respond( $match = array() )
    {
        $this->responded = true;
        return $this->name;
    }
}
