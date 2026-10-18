<?php

namespace Modules\Employee\DTO;

use Illuminate\Support\Facades\Hash;

class EmployeeDto
{
    public $name;
    public $phone;
    public $email;
    public $password;
    public $school_id;
    public $role_id;

    public function __construct($request)
    {
        if ($request->get('name'))
            $this->name = $request->get('name');
        if ($request->get('phone'))
            $this->phone = $request->get('phone');
        if ($request->get('email'))
            $this->email = $request->get('email');
        if ($request->get('password'))
            $this->password = Hash::make($request->get('password'));
        $this->school_id = auth('user')->user()->school_id;
        if ($request->get('role_id'))
            $this->role_id = $request->get('role_id');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->name == null)
            unset($data['name']);
        if ($this->phone == null)
            unset($data['phone']);
        if ($this->email == null)
            unset($data['email']);
        if ($this->password == null)
            unset($data['password']);
        if ($this->role_id == null)
            unset($data['role_id']);
        return $data;
    }
}
