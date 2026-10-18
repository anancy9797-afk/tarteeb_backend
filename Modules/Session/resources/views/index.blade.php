@extends('session::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('session.name') !!}</p>
@endsection
