<?php

namespace Jstalinko\PaydisiniLaravel;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jstalinko\PaydisiniLaravel\Skeleton\SkeletonClass
 */
class PaydisiniLaravelFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'paydisini-laravel';
    }
}
