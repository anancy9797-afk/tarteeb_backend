@extends('grade::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('grade.name') !!}</p>
@endsection
