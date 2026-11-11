<?php

namespace Modules\Session\DTO;

class LateAttendanceDto
{
    public array $student_ids;

    public function __construct($request)
    {
        $this->student_ids = $request->input('student_ids', []);
    }

    public function dataFromRequest(): array
    {
        return [
            'ids' => $this->student_ids
        ];
    }
}