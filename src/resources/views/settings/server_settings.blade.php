@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Server information</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="col-12">
                <div>
                    <strong>Web server: </strong><span>{{ $serverObj->web_server }}</span>
                </div>
                <div><strong>User agent: </strong><span>{{ $serverObj->http_user_agent }}</span></div>
                <div><strong>Gateway interface: </strong><span>{{ $serverObj->gateway_interface }}</span></div>
                <div><strong>Server protocol: </strong><span>{{ $serverObj->server_protocol }}</span></div>
                <div><strong>PHP Version: </strong><span>{{ $serverObj->php_version }}</span></div>
                <div><strong>PHP Url: </strong><span>{{ $serverObj->php_url }}</span></div>
                <div><strong>OS: </strong><span>{{ $serverObj->os }}</span></div>
                <div><strong>OS architecture: </strong><span>{{ $serverObj->ar }}</span></div>
            </div>
        </div>
    </div>
@endsection
