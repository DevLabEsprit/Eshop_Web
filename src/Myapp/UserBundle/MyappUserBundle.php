<?php

namespace Myapp\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MyappUserBundle extends Bundle
{
    public function getParent(){
        return 'FOSUserBundle';
    }

}
