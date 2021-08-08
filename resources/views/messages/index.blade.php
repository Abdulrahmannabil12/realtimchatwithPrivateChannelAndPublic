@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h3> Online Users</h3>
                <hr>
                <ul class="list-group">
                    <li class="list-group-item" id="online-users"></li>
                </ul>
                <h6 id="no-active-users"> No Online Users</h6>
                <hr>
            </div>
            <div class="col-md-9 d-flex flex-column" style="height: 80vh;">
                <div class="h-100 bg-white p-4 mb-4" id="chat" style="overflow: scroll;height:30px;">
                    @foreach($messages as $message)
                        <div id="message-{{$message->id}}"
                             class="mt-4 w-50 text-white p-3 rounded  {{auth()->user()->id ==$message->user_id ? 'float-right  bg-primary':'float-left bg-warning'}}">
                            <p>{{$message->body}} </p>
                        </div>
                        <div class="clearfix"></div>
                    @endforeach
                </div>
                <form method="post" id="formId" class="d-flex">
                    @csrf
                    <input type="text" data-url="{{route('message.store')}}" name="body" class="form-control"
                           style="margin-right: 20px" id="chat-text">
                    <button class="btn btn-primary"> Send</button>
                </form>
            </div>
        </div>
    </div>


@endsection
