<?php


namespace Modules\Session\DTO;

class AttendanceDto
{
    public $date;
    public array $attendance;

    public function __construct($request)
    {
        if ($request->get('date'))
            $this->date = $request->get('date');
        if ($request->get('attendance'))
            $this->attendance = $request->get('attendance');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->date == null)
            unset($data['date']);
        if ($this->attendance == null)
            unset($data['attendance']);
        return $data;
    }
}

