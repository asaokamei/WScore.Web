<?php
namespace WScore\tests\Mock;

class SelfLoader extends \WScore\Web\Loader\LoaderAbstract
{
    var $pre_load = false;
    var $post_load = false;
    var $loaded = false;
    var $path = null;
    public function pre_load() {
        $this->pre_load = true;
    }
    public function load( $pathInfo ) {
        $this->loaded = true;
        $this->path   = $pathInfo;
    }
    public function post_load() {
        $this->post_load = true;
    }
}
