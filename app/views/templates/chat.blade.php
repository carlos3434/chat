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
                <a @click.prevent="showModal" class="btn btn-sm btn-default" role="button"><i class="fa fa-plus"></i> New Message</a>
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
                    <textarea @keyup.prevent="handleKeypress" id='message' v-model='messageBox' class="form-control send-message" rows="3"></textarea>
                    <div class="send-message">
                        <a @click.prevent="sendMessage" :disabled="messageBox.trim()===''" class="text-right btn btn-sm btn-danger pull-right" role="button"><i class="fa fa-send"></i> Enviar mensaje</a>
                    </div>
                </template>
            </div>
        </div>
        @include('templates/new_message_modal')
    </div>
@stop

@section('scripts')
    <script>
        var user_id="{{ Auth::user()->id }}";
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
                user_id: user_id,
                socket:[],
                messageBox:'',
                body:''
            },
            ready: function () {
                this.scrollToBottom();
                var socket = io('http://chat:3000')

                this.$http.get("/users/" + user_id + '/conversations',function(response) {
                    if(response.success && response.result.length > 0) {
                        $.each(response.result, function(index, conversation) {
                            socket.emit('join', { room:  conversation.name });
                        });
                    }
                });
                /***
                    Socket.io Events
                ***/
                socket.on('welcome', function (data) {
                    socket.emit('join', { room:  user_id });
                });
                socket.on('joined', function(data) {
                    //console.log(data.message);
                });
                socket.on('chat.messages', function(data) {
                    vm.chat(vm.current_conversation.name);
                });
                socket.on('chat.conversations', function(data) {
                   vm.chat(vm.current_conversation.name);
                });
                this.getAreas();
                this.chat();
            },
            methods: {
                changeArea: function(){
                    this.$http.get("/areas/"+this.area_id+"/users",function(response) {
                        this.users=response.users;
                    });
                },
                changeUser: function(){
                    this.body='';
                    $('#new_message').focus();
                },
                getAreas: function(){
                    this.$http.get("/areas",function(response) {
                        vm.areas=response.areas;
                    });
                },
                chat: function (conversation) {
                    var request={conversation: conversation };
                    this.$http.post("/chat",request,function(response) {
                        this.current_conversation = response.current_conversation;
                        this.conversations= response.conversations;
                        this.scrollToBottom();
                    });
                },
                sendMessage: function() {
                    data=  { 
                        body: this.messageBox ,
                        conversation: this.current_conversation.name,
                        user_id: this.user_id 
                    };
                    if (this.messageBox.trim()=='') return;
                    this.$http.post("/messages",data,function(data) {
                        this.messageBox='';
                    });
                },
                sendConversation: function(){
                    var usuarios;
                    if (this.users_id=='Seleccione usuario') {
                        return;
                    }
                    if (!Array.isArray(this.users_id)){
                        usuarios = [this.users_id];
                    } else {
                        usuarios = this.users_id;
                    }
                    request={
                        body:this.body,
                        users:usuarios
                    };
                    this.$http.post("/conversations",request,function(response) {
                        this.chat(response.conversation);
                        this.body='';
                        $('#newMessageModal').modal('hide');
                    });
                },
                handleKeypress: function(event) {
                    if (event.keyCode == 13 && event.shiftKey) {
                    } else if (event.keyCode == 13){
                        if (this.messageBox.trim()=='') return;
                        this.sendMessage();
                    }
                },
                handleKeypressModal: function(event) {
                    if (event.keyCode == 13 && event.shiftKey) {
                    } else if (event.keyCode == 13){
                        if (this.body.trim()=='') return;
                        this.sendConversation();
                    }
                },
                showModal: function () {
                    this.area_id='';
                    this.users_id='';
                    this.body='';
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
                    this.messageBox='';
                    $('#message').focus();
                }
            }
        });
    </script>
@stop