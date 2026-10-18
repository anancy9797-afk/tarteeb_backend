@extends('class::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('class.name') !!}</p>
@endsection
