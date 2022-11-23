<?php

namespace OmniForm\Dependencies\Illuminate\Contracts\Container;

use Exception;
use OmniForm\Dependencies\Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
