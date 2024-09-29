<?php

namespace App\Plugins\Http\Response;

use App\Plugins\Http\JsonStatusFail;

class Unauthorized extends JsonStatusFail {
    /** @var int */
    const STATUS_CODE = 401;
    /** @var string */
    const STATUS_MESSAGE = 'Unauthorized';

    /**
     * Constructor of this class
     * @param mixed $body
     */
    public function __construct($body = '') {
        parent::__construct(self::STATUS_CODE, self::STATUS_MESSAGE, $body);
    }
}
