@extends('layouts.app')

@section('title', __('Your devices'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-8">
            <div class="row">
                @foreach($devices->all() as $device)
                    <div class="col-lg-4 col-sm-6 col-xs-12 justify-content-center px-1 pb-2">
                        @include('devices.card')
                    </div>
                    @if($loop->iteration % 3 == 0)
                        {{--<div class="w-100"></div>--}}
                        {!! '</div><div class="row">' !!}
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col col-md-4">
            <a href="{{ route('devices.create') }}"
               class="btn btn-primary btn-lg btn-block border border-dark">{{ __('Add device') }}</a>
        </div>
    </div>
@endsection

@section('modalboxes')
    <div id="connectModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white"></h4>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body text-center"></div>
                <form id="machAction" action="" method="post" target="_blank">
                    <input id="machToken" type="hidden" name="access_token" value="" />
                </form>
                <div id="connectWait" style="display:none">
                    <p><div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar" aria-valuenow="0" aria-valuemin="0"
                             aria-valuemax="100" style="width: 0%"></div>
                    </div></p>
                </div>
                <div id="connectOk" style="display:none">
                    <h1><i class="fas fa-check-circle" style="color:green"></i></h1>
                    <p>{{ __('Link has been established.') }}<br/>
                        {{ __('Click "Back" to continue working or') }}<br/>
                        {{ __('"Disconnect" button to close link.') }}</p>
                    <p><button class="btn btn-secondary border border-dark float-left"
                               type="button" data-dismiss="modal">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}</button>
                        <button class="btn btn-danger border border-dark float-right disconnect"
                                type="button">
                            <i class="fas fa-hand-paper"></i> {{ __('Disconnect') }}</button></p>
                </div>
                <div id="connectError" style="display:none">
                    <h1><i class="fas fa-times-circle" style="color:red"></i></h1>
                    <p>{{ __('Couldn\'t establish the link.') }}<br/>
                        {{ __('Please try again in a minute.') }}</p>
                    <p><button class="btn btn-secondary border border-dark"
                               type="button" data-dismiss="modal">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}</button></p>
                </div>
                <div id="disconnectOk" style="display:none">
                    <h1><i class="fas fa-check-circle" style="color:green"></i></h1>
                    <p>{{ __('Link has been closed successfully.') }}</p>
                    <p><button class="btn btn-secondary border border-dark float-right"
                               type="button" data-dismiss="modal">
                            <i class="fas fa-check"></i> {{ __('Close') }}</button></p>
                </div>
                <div id="disconnectError" style="display:none">
                    <h1><i class="fas fa-times-circle" style="color:red"></i></h1>
                    <p>{{ __('Couldn\'t close the link.') }}<br/>
                        {{ __('Please try again in a minute.') }}</p>
                    <p><button class="btn btn-secondary border border-dark"
                               type="button" data-dismiss="modal">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}</button></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        let target;
        let progbar = 0;
        $(document).ready(function() {
            $('#connectModal').on('show.bs.modal', function (e) {
                target = $(e.relatedTarget);
                progbar = 0;
                $('.modal-title').text(target.data('title'));
                $('.modal-body').html($('#connectWait').html());
                $('.progress-bar').css('width', '0%').attr('aria-valuenow', 0);
                setTimeout(function () {
                    progbar = progbar + 5;
                    $('.progress-bar').css('width', progbar+'%').attr('aria-valuenow', progbar);
                },1000);
                connect_tunnel(target);
            });
        });
        $('#connectModal').on('click', 'button.disconnect', function () {
            disconnect_tunnel(target);
        });

        let connect_tunnel = (target) =>
        {
            $.ajax({
                url: target.data('connect-url'),
                method: "GET",
                dataType: "json",
                success: function (result) {
                    status_tunnel(target, 1);
                },
                error: function (req, status, error) {
                    $('.modal-body').html($('#connectError').html());
                }
            });
        };

        let status_tunnel = (target, retries) =>
        {
            $.ajax({
                url: target.data('status-url'),
                method: "GET",
                dataType: "json",
                success: function (result) {
                    $('.progress-bar').css('width', '100%').attr('aria-valuenow', 100);
                    setTimeout(function () {
                        $('.modal-body').html($('#connectOk').html());
                        run_tunnel(result);
                    }, 2000);
                },
                error: function (req, status, error) {
                    if (req.status === 408 || retries > 6) {
                        $('.modal-body').html($('#connectError').html());
                    } else {
                        retries++;
                        progbar = progbar + retries * 3;
                        $('.progress-bar').css('width', progbar + '%').attr('aria-valuenow', progbar);
                        setTimeout(function () {
                            status_tunnel(target, retries);
                        }, 5000);
                    }
                }
            });
        };

        let run_tunnel = (result) =>
        {
            $('#machToken').attr('value', result.response_body.access_token);
            $('#machAction').attr({
                action: '/remote/' + result.response_body.tunnel_uuid + '/auth.php',
                method: 'POST'
            }).submit();
        };

        let disconnect_tunnel = (target) =>
        {
            $.ajax({
                url: target.data('disconnect-url'),
                method: "GET",
                dataType: "json",
                success: function (result) {
                    $('.modal-body').html($('#disconnectOk').html());
                },
                error: function (req, status, error) {
                    $('.modal-body').html($('#disconnectError').html());
                }
            });
        };

    </script>
@endsection