<?php


namespace Modules\User\DTO;

use Illuminate\Support\Facades\Hash;


class SchoolManagerDto
{
    public $name;
    public $email;
    public $phone;
    public $password;

    public function __construct($request) {
        if($request->get('name')) $this->name = $request->get('name');
        if($request->get('email')) $this->email = $request->get('email');
        if($request->get('phone')) $this->phone = $request->get('phone');
        if($request->get('password')) $this->password = Hash::make($request->get('password'));
    }

    public function dataFromRequest()
    {
        $data =  json_decode(json_encode($this), true);
        if($this->name == null) unset($data['name']);
        if($this->email == null) unset($data['email']);
        if($this->phone == null) unset($data['phone']);
        if($this->password == null) unset($data['password']);
        return $data;
    }
}
