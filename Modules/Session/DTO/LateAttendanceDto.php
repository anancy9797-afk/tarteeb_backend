<?php

namespace Modules\Session\DTO;

class LateAttendanceDto
{
    public array $ids;

    public function __construct($request)
    {
        $this->ids = $request->input('ids', []);
    }

    public function dataFromRequest(): array
    {
        return [
            'ids' => $this->ids
        ];
    }
}