document.addEventListener('DOMContentLoaded', function() {
    //initialize Pusher
    var pusher = new Pusher('615d0825e7e5e8e7af1f', {
        cluster: 'eu',
        encrypted: true
    });

    var channel = pusher.subscribe('messages');

    channel.bind('NewMessage', function(data) {
        //log data for debugging
        console.log('Received new message:', data);

        //check if the authenticated user is the receiver
        if (data.message.receiver_id == authUserId) {
            var senderName = data.sender_name;
            var messageContent = data.message.message_content;

            //check if the user is not on the /messages page
            if (window.location.pathname !== '/messages') {
                //play the alert sound 
                document.getElementById('alertButton').click(); 

                //show an alert message
                alert('New message from ' + senderName + ': ' + messageContent);
            }
        }
    });

    //function to play the alert sound
    function playAlertSound() {
        var alertSound = document.getElementById('alertSound');
        alertSound.play();
    }

    //bind the playAlertSound function to a button click
    document.getElementById('alertButton').addEventListener('click', playAlertSound);
});
