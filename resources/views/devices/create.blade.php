@extends('layouts.app')

@section('title', __('Create device'))

@section('content')
    <form id="devices-create-form" action="{{ route('devices.store') }}" method="POST">
        @csrf
        @include('devices.form')
    </form>
@endsection