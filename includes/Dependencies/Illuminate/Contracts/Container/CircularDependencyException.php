<?php

namespace InquiryWP\Dependencies\Illuminate\Contracts\Container;

use Exception;
use InquiryWP\Dependencies\Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
