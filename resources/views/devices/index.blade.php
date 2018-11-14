@extends('layouts.app')

@section('title', __('Your devices'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card-deck">
                @foreach($devices->all() as $device)
                    <div class="card text-white bg-primary border-dark rounded" style="margin:0.5rem">
                        <div class="card-header">
                            <div class="float-right align-self-center"><img src="{{ asset('images/machinon_icon.png') }}" class="card-icon" alt="icon" /></div>
                            <h4>{{ $device->name }}</h4>
                            <small class="card-subtitle mb-2 text-secondary">{{ $device->muid }}</small>
                        </div>
                        <div class="card-body bg-light text-dark">
                            <p class="card-text">{{ $device->description }}</p>
                        </div>
                        <div class="card-footer text-muted">
                            <a href="{{ route('devices.edit', ['id' => $device->id]) }}" class="btn btn-primary float-left" alt="Edit">
                                <i class="fas fa-pencil-alt"></i></a>
                            <a href="{{ route('devices.show', ['id' => $device->id]) }}" class="btn btn-primary" alt="Show">
                                <i class="far fa-eye"></i></a>
                            <a href="{{ route('devices.connect', ['id' => $device->device_tunnel->id]) }}" class="btn btn-success float-right" alt="Connect">
                                <i class="far fa-handshake"></i></a>
                        </div>
                    </div>
                    @if($loop->iteration % 3 == 0)
                        <div class="w-100"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col col-md-4">
            <a href="{{ route('devices.create') }}" class="btn btn-primary btn-lg btn-block">{{ __('Add device') }}</a>
        </div>
    </div>
@endsection