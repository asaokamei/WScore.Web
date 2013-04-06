<?php
namespace WScore\Web\Page;

interface PageInterface
{
    const RELOAD_SELF       = true;
    const JUMP_TO_APP_ROOT  = '';
    const RENDER_PAGE       = null;
    const RENDER_NOTHING    = false;
}