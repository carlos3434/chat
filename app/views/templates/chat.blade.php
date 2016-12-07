@extends('layouts/main')

@section('body')
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/chat">Realtime Chat</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <img class="img-circle" width="30" height="30" src="{{ Auth::user()->image_path }}"/>
                            {{ Auth::user()->username }}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Home</a></li>

                            <li class="divider"></li>
                            <li><a href="{{ action('AuthController@logout') }}">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container" id='chat'>
        <div class="row">
            <div class="col-lg-3 new-message text-right">
                <a id="btnNewMessage" class="btn btn-sm btn-default" role="button"><i class="fa fa-plus"></i> New Message</a>
            </div>
        </div>
        <div class="row">
            <div id="conversationList">
                @include('templates/conversations')
            </div>
            <div class="col-lg-8">
                <template v-if="current_conversation.name">
                    <div class="panel panel-default">
                        <div id="messageList" class="panel-body messages-panel">
                            @include('templates/messages')
                        </div>
                    </div>
                    <textarea @keyup.prevent="handleKeypress" v-model='messageBox' class="form-control send-message" rows="3" placeholder="Escribe una respuesta..."></textarea>
                    <div class="send-message">
                        <a @click.prevent="sendMessage" class="text-right btn btn-sm btn-danger pull-right" role="button"><i class="fa fa-send"></i> Enviar mensaje</a>
                    </div>
                </template>
            </div>
        </div>
        @include('templates/new_message_modal')
    </div>
@stop

@section('scripts')
    <script>
        var
            current_conversation = "{{ Session::get('current_conversation') }}",
            user_id   = "{{ Auth::user()->id }}";
        var vm = new Vue({
            http: {
                root: '/root'
            },
            el: '#chat',
            data: {
                conversations:[],
                current_conversation:[],
                messages:[],
                areas:[],
                users:[],
                user_id: "{{ Auth::user()->id }}",
            },
            ready: function () {
                //cargar usuarios
                //cargar areas
                //notificar cuando badge a pesar que sea conversationn_current
                this.getAreas();
                this.chat();
            },
            methods: {
                changeArea: function(){
                    this.$http.get("/areas/"+this.area_id+"/users",function(response) {
                        this.users=response.users;
                    });
                },
                getAreas: function(){
                    this.$http.get("/areas",function(response) {
                        vm.areas=response.areas;
                    });
                },
                getAreas: function(){
                    this.$http.get("/areas",function(response) {
                        vm.areas=response.areas;
                    });
                },
                chat : function (conversation) {
                    var request={conversation: conversation };
                    this.$http.post("/chat",request,function(response) {
                        this.current_conversation = response.current_conversation;
                        this.conversations= response.conversations;
                        this.scrollToBottom();
                    });
                },
                sendMessage: function() {
                    //var $messageBox  = $("#messageBox");
                    data=  { 
                        body: this.messageBox ,
                        conversation: this.current_conversation.name,
                        user_id: user_id 
                    };
                    //var $messageBox  = $("#messageBox");
                    if (this.messageBox.trim()=='') return;
                    this.$http.post("/messages",data,function(data) {
                        this.messageBox='';
                    });
                },
                sendConversation: function(env){
                    var usuarios;
                    if (!Array.isArray(this.users_id)){
                        usuarios = [this.users_id];
                    }else{
                        usuarios = this.users_id;
                    }

                    data={
                        body:this.body,
                        users:usuarios
                    };
                    this.$http.post("/conversations",data,function(data) {
                        getConversations(this.current_conversation.name);
                        $('#newMessageModal').modal('hide');
                        this.body='';
                    });
                },
                handleKeypress: function(event) {
                    if (event.keyCode == 13 && event.shiftKey) {
                    } else if (event.keyCode == 13){
                        //var $messageBox  = $("#messageBox");
                        if (this.messageBox.trim()=='') return;
                        this.sendMessage();
                    }
                },
                showModal: function (){
                    $('#newMessageModal').modal('show');
                },
                scrollToBottom: function() {
                    this.handle = setInterval( ( ) => {
                        var $messageList  = $("#messageList");

                        if($messageList.length) {
                            $messageList.animate({scrollTop: $messageList[0].scrollHeight}, 500);
                        }
                        clearInterval(this.handle);
                    },1);
                }
            }
        });
    </script>
    <script src="{{ asset('/js/chat.js')}}"></script>
@stop

