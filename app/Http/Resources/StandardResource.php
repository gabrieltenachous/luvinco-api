<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StandardResource extends JsonResource
{
    protected string $message;
    protected $payload;

    public function __construct(string $message, $payload)
    {
        parent::__construct(null);
        $this->message = $message;
        $this->payload = $payload;
    }

    public function toArray(Request $request): array
    {
        return [
            'message' => $this->message,
            'data' => $this->payload,
        ];
    }
}
