<?php

namespace PluginWP\Dependencies\Illuminate\Container;

use Exception;
use PluginWP\Dependencies\Psr\Container\NotFoundExceptionInterface;

class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
