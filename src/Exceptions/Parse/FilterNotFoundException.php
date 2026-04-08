<?php

namespace Laravue2\RestAPI\Exceptions\Parse;

use Laravue2\RestAPI\Exceptions\ApiException;
use Laravue2\RestAPI\Exceptions\ErrorCodes;

class FilterNotFoundException extends ApiException
{

    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::INNER_FILTER_NOT_FOUND;

    protected $message = "Requested filter not found";

}