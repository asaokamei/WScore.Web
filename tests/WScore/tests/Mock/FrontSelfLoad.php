<?php
namespace WScore\tests\Mock;

class FrontSelfLoad extends \WScore\Web\FrontMC
{
    /**
     * @Inject
     * @param \WScore\tests\Mock\SelfLoader1 $var1
     * @param \WScore\tests\Mock\SelfLoader2 $var2
     * @param \WScore\tests\Mock\SelfLoader1 $var3
     */
    public function load( $var1, $var2, $var3 )
    {
        $this->loaders[] = $var1;
        $this->loaders[] = $var2;
        $this->loaders[] = $var3;
    }
}