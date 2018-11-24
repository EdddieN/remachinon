<div class="card text-white bg-primary border-dark rounded" style="width:100%;height:100%">
    <div class="card-header">
        <div class="float-right align-self-center">
            <img src="{{ asset('images/machinon_icon.png') }}"
                 class="card-icon" alt="icon" /></div>
        <h4>{{ $device->name }}</h4>
        {{--<small class="card-subtitle mb-2 text-secondary">{{ $device->muid }}</small>--}}
    </div>
    <div class="card-body bg-light text-dark">
        <p class="card-text">{{ $device->description }}</p>
    </div>
    <div class="card-footer text-muted">
        <a href="{{ route('devices.edit', ['id' => $device->id]) }}"
           class="btn btn-primary float-left border border-dark">
            <i class="fas fa-pencil-alt"></i></a>
        <a href="{{ route('devices.show', ['id' => $device->id]) }}"
           class="btn btn-primary border border-dark">
            <i class="far fa-eye"></i></a>
        {{--<a href="{{ route('devices.connect', ['id' => $device->device_tunnel->id]) }}"--}}
           {{--class="btn btn-success float-right border border-dark" target="_blank">--}}
            {{--<i class="far fa-handshake"></i></a>--}}
        <button type="button"
                data-url="{{ route('devices.connect', ['id' => $device->device_tunnel->id]) }}"
                data-poll="{{ route('api.tunnels.status', ['id' => $device->device_tunnel->id]) }}"
                data-id="{{ $device->device_tunnel->id }}"
                data-title="Connecting to {{ $device->name }}"
                data-toggle="modal"
                data-target="#connectModal"
                class="btn btn-danger float-right border border-dark">
            <i class="far fa-handshake"></i></button>
    </div>
</div>