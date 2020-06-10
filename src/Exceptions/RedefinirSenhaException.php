<?php

namespace JbAuthJwt\Exceptions;

use JbGlobal\Exceptions\AppException;

/**
* AuthException
*/
class RedefinirSenhaException extends AppException
{
    protected $nivel = self::LOG_NIVEL_ERROR;
}
