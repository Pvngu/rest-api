<?php

namespace Laravue2\RestAPI\Facades;

use Laravue2\RestAPI\Routing\ApiRouter;
use Illuminate\Support\Facades\Facade;

class ApiRoute extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ApiRouter::class;
    }
}