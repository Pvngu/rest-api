<?php

namespace Laravue2\RestAPI\Exceptions\Parse;

use Laravue2\RestAPI\Exceptions\ApiException;
use Laravue2\RestAPI\Exceptions\ErrorCodes;

class InvalidLimitException extends ApiException
{
    protected $statusCode = 422;

    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innercode = ErrorCodes::INNER_INVALID_LIMIT;

    protected $message = "Limit cannot be negative or zero";
}