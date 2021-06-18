require('./bootstrap');
import Echo from "laravel-echo"

window.io = require('socket.io-client');

window.Echo = new Echo({
    namespace: 'App.Events',
    broadcaster: 'socket.io',
    host: window.location.protocol + '//' + window.location.hostname + ':6001',
});
let OnlineUsersLength = 0;
window.Echo.join(`Online`)
    .here((users) => {
        OnlineUsersLength = users.length;
        console.log(OnlineUsersLength);
        if (OnlineUsersLength > 1) {
            $('#no-active-users').css('display', 'none')
        }
        let UserId = $('meta[name=user-id]').attr('content');

        users.forEach(function (user) {
                if (user.id == UserId) {
                    return;
                }
                let url = $('meta[name=url]').attr('content') + '/';
                $('#online-users').append(` <a href="${url}message/private/${user.id}"><li class="list-group-item" id="user-${user.id}">${user.name}</li> <a/>`);
            }
        );
        console.log(users)
    })
    .joining((user) => {
        $('#no-active-users').css('display', 'none')

        $('#online-users').append(`<li class="list-group-item" id="user-${user.id}">${user.name}</li>`);

        console.log("join" + user.id);
    })
    .leaving((user) => {
        $('#user-' + user.id).remove();

        console.log("left" + user.id);
    });


$('#chat-text').keypress(function (e) {

    if (e.which == 13) {
        e.preventDefault();

        let message = $(this).val();
        let url = $(this).data('url')
        let data = {
            '_token': $('meta[name=csrf-token]').attr('content'),
            'body': message,
        }
        $(this).val('');

        $.ajax({
            url: url,
            method: 'post',
            data: data,
            success: function (response) {
                if (response) {
                    let message_id = response.data.id;
                    let id = 'message-' + message_id;

                    console.log(id)


                    $('#chat')
                        .append(`<div class="mt-4 w-50 text-white p-3 rounded  float-right bg-primary" id="${id}"> <p>${message} </p> </div>  <div class="clearfix"></div> `)
                    $('#message-' + message_id)[0].scrollIntoView()


                }
            },

        })


    }
});
window.Echo.channel('chat-group')
    .listen('MessageDeliverd', (e) => {
        let message = e.message.body;
        let message_id = e.message.id;
        let id = 'message-' + message_id;
        $('#chat')
            .append(`<div class="mt-4 w-50 text-white p-3 rounded  float-left bg-warning" id="${id}"> <p>${message} </p> </div>  <div class="clearfix"></div> `)
        $('#message-' + message_id)[0].scrollIntoView()

    })


/* begin of private channels*/
$('#private-chat-text').keypress(function (e) {

    if (e.which == 13) {
        e.preventDefault();

        let message = $(this).val();
        let url = $(this).data('url')
        let data = {
            '_token': $('meta[name=csrf-token]').attr('content'),
            'message_type': 'text',
            'body': message,
        }
        $(this).val('');

        $.ajax({
            url: url,
            method: 'post',
            data: data,
            success: function (response) {
                if (response) {
                    console.log(response)
                    let message_id = response.data.id;
                    let id = 'message-' + message_id
                    $('#private-chat')
                        .append(`<div class="mt-4 w-50 text-white p-3 rounded  float-right bg-primary" id="${id}"> <p>${message} </p> </div>  <div class="clearfix"></div> `)
                    $('#message-' + message_id)[0].scrollIntoView()


                }
            },

        })


    }
});

let conversationId = $('meta[name=conversationId]').attr('content');
console.log($('meta[name=conversationId]').attr('content'))

 window.Echo.private(`chat.${conversationId}`)
    .listen('PrivateMessage', (e) => {
        console.log(e.message.body)

        let message = e.message.body;
        let message_id = e.message.id;
        let id = 'message-' + message_id;
        $('#private-chat')
            .append(`<div class="mt-4 w-50 text-white p-3 rounded  float-left bg-warning" id="${id}"> <p>${message} </p> </div>  <div class="clearfix"></div> `)
        $('#message-' + message_id)[0].scrollIntoView()

    });

