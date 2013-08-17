<?php
namespace WScore\Web\Session;

trait TokenTrait
{
    /**
     * @Inject
     * @param \WScore\Web\Session\TokenModule $token
     */
    public function setCsrfToken( $token )
    {
        $this->setPreparation( $token );
    }
}