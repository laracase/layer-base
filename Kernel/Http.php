<?php

namespace Layer\Base\Kernel;

use Illuminate\Foundation\Http\Kernel;

class Http extends Kernel
{
    public function addMiddlewareAliases($alias, $middleware)
    {
        $this->middlewareAliases[$alias] = $middleware;
        $this->syncMiddlewareToRouter();

        return $this;
    }


    public function addMiddlewareGroup($group, $middleware)
    {
        if (array_search($middleware, $this->middlewareGroups[$group]) === false) {
            array_unshift($this->middlewareGroups[$group], $middleware);
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }
}
