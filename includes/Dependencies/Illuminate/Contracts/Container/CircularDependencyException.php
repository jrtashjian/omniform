<?php

namespace PluginWP\Dependencies\Illuminate\Contracts\Container;

use Exception;
use PluginWP\Dependencies\Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
