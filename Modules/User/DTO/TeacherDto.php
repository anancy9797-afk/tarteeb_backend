<?php


namespace Modules\User\DTO;

use Illuminate\Support\Facades\Hash;


class TeacherDto
{
    public $name;
    public $email;
    public $phone;
    public $password;
    public $school_id;

    public function __construct($request)
    {
        if ($request->get('name'))
            $this->name = $request->get('name');
        if ($request->get('email'))
            $this->email = $request->get('email');
        if ($request->get('phone'))
            $this->phone = $request->get('phone');
        if ($request->get('password'))
            $this->password = Hash::make($request->get('password'));
        if (auth('user')->user()->hasRole('Super Admin')) {
            if ($request->get('school_id')) {
                $this->school_id = $request->get('school_id');
            }
        } else if (auth('user')->user()->hasRole('School Manager')) {
            if ($request->isMethod('POST')) {
                $this->school_id = auth('user')->user()->school_id;
            }
        }
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->name == null)
            unset($data['name']);
        if ($this->email == null)
            unset($data['email']);
        if ($this->phone == null)
            unset($data['phone']);
        if ($this->password == null)
            unset($data['password']);
        if ($this->school_id == null)
            unset($data['school_id']);
        return $data;
    }
}

