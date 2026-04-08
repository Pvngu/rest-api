<?php

namespace Laravue\RestAPI\Exceptions\Parse;

use Laravue\RestAPI\Exceptions\ApiException;
use Laravue\RestAPI\Exceptions\ErrorCodes;

class InvalidFilterDefinitionException extends ApiException
{

    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::INNER_INVALID_FILTER_DEFINITION;

    protected $message = "Filter defined incorrectly";

}