@extends('country::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('country.name') !!}</p>
@endsection
