@extends('layouts.app')

@section('title', __('Edit device'))

@section('content')
    <form id="devices-create-form" action="{{ route('devices.update', ['id' => $device->id]) }}" method="POST">
        @method('PATCH')
        @csrf
        @include('devices.form')
    </form>
@endsection