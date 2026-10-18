<?php


namespace Modules\School\DTO;

class SchoolSettingDto
{
    public $ultramsg_token;
    public $ultramsg_instance_id;

    public function __construct($request)
    {
        if ($request->get('ultramsg_token'))
            $this->ultramsg_token = $request->get('ultramsg_token');
        if ($request->get('ultramsg_instance_id'))
            $this->ultramsg_instance_id = $request->get('ultramsg_instance_id');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->ultramsg_token == null)
            unset($data['ultramsg_token']);
        if ($this->ultramsg_instance_id == null)
            unset($data['ultramsg_instance_id']);
        return $data;
    }
}
