@extends('admin.layouts.app')
@section('title')
Conversations Management
@endsection
@section('mainContent')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Add Conversations</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.conversations.index') }}">Conversations Table</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Add Conversations Form</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="wrapper wrapper-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox ">
                                <div class="ibox-content">
                                    <div class="col-md-12">
                                        <div class="table-responsive" style="overflow-x:hidden;">
                                            <div class="list_box">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-12 desk-p-right" style="overflow: scroll; height: 453px;">
                                                        <div class="scrollDiv">
                                                            <div class="list-group usersList">
                                                                @if($newuser->count() > 0)
                                                                @foreach($newuser as $ChatUser)
                                                                <a href="JavaScript:void(0);" class="list-group-item list-group-item-action getMessages" data-toggle="modal" data-target="#xlarge" sent-to="{{$ChatUser->id}}" sent-from="{{$userID}}">
                                                                    @if($ChatUser->avatar === '' || $ChatUser->avatar === 'default.png')
                                                                    <img src="{!! asset('storage/avatar/default.png') !!}" alt="user-img" class="img-circle" accept="image/*" style="height: 30px; width: 30px; border-radius: 50%; border: 3px solid transparent; padding: 0px; box-shadow: 2px 2px 10px; background-color: #FFF;">
                                                                    @else
                                                                    <img src="{{ asset('storage/avatar/'.$ChatUser->avatar) }}" style="height: 30px; width: 30px; border-radius: 50%; border: 3px solid transparent; padding: 0px; box-shadow: 2px 2px 10px; background-color: #FFF;">
                                                                    @endif
                                                                    <span style="margin-left: 10px;">{{$ChatUser->email}}</span>
                                                                </a>
                                                                @endforeach
                                                                @else
                                                                <div class="list-group-item list-group-item-action getMessages">
                                                                    <b>Record Not Found</b>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 desk-p-left">
                                                        <div class="conversationContainer"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .list-group-item{background-color:#FFF;}
    .messageWrap .messageAvatar img {width: 40px; height: 40px; float: left; position: relative; margin-right: 15px; border-radius: 50%;}
    .usersList img{width: 40px; height: 40px; border-radius: 50%;}
    .conversationContainer {text-align: justify; border: 1px solid whitesmoke; padding: 10px; margin-left: 10px; margin-right: 10px; border-radius: 10px; height: 450px; position: relative; overflow-x: hidden; box-shadow: 1px 1px 9px 0px rgba(0,0,0,0.3); background-color: #FFF;}
    .list-group-item.active {z-index: 2; color: #fff; background-color: #404e66 !important; border-color: #404e66 !important;}
</style>
@endsection
@section('scripts')
    <script>
        $(document).on('click', '.getMessages', function (e) {
            var row = $(this);
            var sent = $(this).attr('sent-to');
            var from = $(this).attr('sent-from');
            $.ajax({
                url     : "{{ route('admin.conversations.converstionList') }}",
                method  : 'get',
                data    : {
                    sent  : sent, from : from
                },
                success:function(response){
                    $('.conversationContainer').html(response.html).scrollTop($(".conversationContainer")[0].scrollHeight);
                    },
                error:function(){
                    swal("Error!", 'Error in delete Record', "error");
                }
            });
        });

        $(document).on('click', '.getMessages', function (e) { 
                var $this = $(this);
                $('.getMessages').removeClass('active');
                if (!$this.hasClass('active')) {
                    $this.addClass('active');
                }
        });
    </script>
@endsection