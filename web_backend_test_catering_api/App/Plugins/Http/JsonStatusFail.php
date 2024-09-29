<?php

namespace App\Plugins\Http;

abstract class JsonStatusFail extends JsonStatus {
    /**
     * @inheritDoc
     */
    public function getBody(): string {
        return json_encode(['statusCode' => $this->code, 'error' => $this->body]);
    }
}
