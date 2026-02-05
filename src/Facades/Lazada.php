<?php

namespace Laraditz\Lazada\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laraditz\Lazada\Skeleton\SkeletonClass
 */
class Lazada extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lazada';
    }
}
