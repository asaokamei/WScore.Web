<?php
namespace WScore\Web\Session;

trait TokenTrait
{
    /**
     * @Inject
     * @param \WScore\Web\Session\Token $token
     */
    public function setCsrfToken( $token )
    {
        $this->setPreparation( $token );
    }
}