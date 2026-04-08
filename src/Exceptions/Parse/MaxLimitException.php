<?php
namespace Laravue\RestAPI\Exceptions\Parse;

use Laravue\RestAPI\Exceptions\ApiException;
use Laravue\RestAPI\Exceptions\ErrorCodes;

class MaxLimitException extends ApiException
{
    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::INNER_MAX_LIMIT;

    protected $message = "Requested more records than maximum limit in single request";
}