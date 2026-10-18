@extends('teacher::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('teacher.name') !!}</p>
@endsection
