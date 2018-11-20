@extends('layouts.app')

@section('title', __('Your devices'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-8">
            <div class="row">
                @foreach($devices->all() as $device)
                    <div class="col-lg-4 col-sm-6 col-xs-12 justify-content-center px-1 pb-2">
                        @include('devices.device-card')
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
                    <h4 class="modal-title text-white" id="modalTitle"></h4>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body text-center">
                    <h1><i class="fas fa-cog fa-spin"></i></h1>
                    <form id="machAction" action=""  method="post" target="_blank">
                        <input id="machlToken" type="hidden" name="access_token" value="" />
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#connectModal').on('show.bs.modal', function (e) {
                let target = $(e.relatedTarget);
                $("#connectModal").find("#modalTitle").text(target.data('title'));
//                poll_tunnel(target);
                tunstat(target);
            });
        });

        {{-- Trying new ES6 function declarations here... ^_^ --}}

        let poll_tunnel = (target) =>
        {
            $.ajax({
                url: target.data('url'),
                method: "GET",
                dataType: "json",
                success: function (result) {
                    let url = '{{ config('app.remote_url') }}/' + result.response_body.uuid + '/';
                    $("#machToken").attr('value', '');
                    $("#machAction").attr('action', url).submit();
                },
                error: function (req, status, error) {
                    console.log('calling poll tunnel at '+target.data('poll'));
//                    poll_tunnel_status(target.data('poll'),0);
                }
            });
        };

        let tunstat = (target) =>
        {
            $.ajax({
                url: target.data('poll')+'?scope=janderclander',
                method: "GET",
                dataType: "json",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X_CSRF_TOKEN': $('meta[name=csrf-token]').attr("content")
                },
                success: function (result) {
                    console.log(result)
                },
                error: function (req, status, error) {
                    console.log(req);
                }
            });
        };

        let poll_tunnel_status = (check_url, tn) =>
        {
            pollingTm = null;
            if (tn === 0) {
                console.log('Tunnel polling started...');
            }
            $.ajax({
                url: check_url,
                method: "GET",
                dataType: "json",
                success: function (result) {
                    //  loginWindow.location.href = tunnel_url;
                    poll_tunnel_stop(pollingTm);
                    console.log('received ' + result + '. redirecting to ' + tunnel_url);
                    $("#machinongo").submit();
                    $("#okmsg").hide();
                    $("#clmsg").show();
                },
                error: function (req, status, error) {
                    console.log('received ' + status + '. waiting for next try...');
                    if (tn > 12) {
                        poll_tunnel_stop(pollingTm);
                        $("#okmsg").hide();
                        $("#ermsg").show();
                    } else {
                        tn = tn + 1;
                        pollingTm = setTimeout(function() {
                            poll_tunnel_status(check_url, tunnel_url, tn);
                        }, 5000);
                    }
                }
            });
        };

        let poll_tunnel_stop = pollingTm =>
        {
            if (pollingTm !== null) clearTimeout(pollingTm);
        };

        let tunnel_close = () =>
        {
            $("#clbtn").attr('value','Please wait...').attr('disabled', true);
            $("#machinonclose").submit();
        };

    </script>
@endsection