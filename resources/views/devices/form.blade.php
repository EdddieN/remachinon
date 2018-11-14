<div class="card border-primary mb-3">
    <div class="card-header"><h4>@yield('title')</h4></div>
    <div class="card-body">
        {{--<h4 class="card-title">Primary card title</h4>--}}
        {{--<p class="card-text"></p>--}}
        <div class="form-group">
            <label for="name">{{ __('Device name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $device->name) }}" required />
        </div>
        <div class="form-group">
            <label for="muid">{{ __('Machinon Unique Identifier (MUID)') }}
                <span class="badge badge-light d-inline-block" role="button" style="cursor:pointer"
                      data-container="body" data-toggle="popover" data-placement="right"
                      data-content="{{ __('image with location of MUID here...') }}"
                      data-original-title="{{ __('Finding the MUID') }} Title">{{ __('Where do I find this?') }}</span></label>
            <input type="text" name="muid" class="form-control"
                   value="{{ old('muid', $device->muid) }}" required />
        </div>
        <div class="form-group">
            <label for="description">{{ __('Description') }}</label>
            <textarea name="description" class="md-textarea form-control">{{ old('description', $device->description) }}</textarea>
        </div>
        {{--<div class="form-group">--}}
        {{--<div class="form-check">--}}
        {{--<input type="checkbox" name="is_enabled" class="form-check-input">--}}
        {{--<label class="form-check-label" for="is_enabled">{{ __('Device enabled') }}</label>--}}
        {{--</div>--}}
        {{--</div>--}}
        @include('form-errors')
        <a href="{{ route('devices.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
        <button type="submit" class="btn btn-success float-right">
            <i class="fas fa-check"></i> {{ __('Save') }}</button>
    </div>
</div>
