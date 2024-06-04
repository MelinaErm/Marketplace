document.addEventListener('DOMContentLoaded', function() {
    //Pusher.logToConsole = true;

    var pusher = new Pusher('4475f46df53f83682842', {
        cluster: 'eu',
        encrypted: true
    });

    var channel = pusher.subscribe('messages');

    channel.bind('NewMessage', function(data) {
        console.log('Received new message:', data);

        //check if the authenticated user is the receiver
        if (data.message.receiver_id == authUserId) {
            var senderName = data.sender_name;
            var messageContent = data.message.message_content;

            //check if the user is not on the /messages page
            if (window.location.pathname !== '/messages') {
                //play the alert sound
                var alertSound = document.getElementById('alertSound');
                alertSound.play();

                //show alert
                alert('New message from ' + senderName + ': ' + messageContent); 
            }
        }
    });
});
