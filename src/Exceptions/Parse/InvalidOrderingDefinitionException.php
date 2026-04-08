<?php

namespace Laravue2\RestAPI\Exceptions\Parse;

use Laravue2\RestAPI\Exceptions\ApiException;
use Laravue2\RestAPI\Exceptions\ErrorCodes;

class InvalidOrderingDefinitionException extends ApiException
{
    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::INNER_ORDERING_INVALID;

    protected $message = "Ordering defined incorrectly";
}