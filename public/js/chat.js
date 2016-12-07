$(function() {

    /***
        Initialization
    ***/

    vm.scrollToBottom();

    var socket = io('http://chat:3000'),

    jqxhr  = $.ajax({
        url: '/users/' + user_id + '/conversations',
        type: 'GET',
        dataType: 'json'
    });

    jqxhr.done(function(data) {
        if(data.success && data.result.length > 0) {
            $.each(data.result, function(index, conversation) {
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
});