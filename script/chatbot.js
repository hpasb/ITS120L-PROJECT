$(document).ready(function(){
    // Toggle the chatbox when the chatbot icon is clicked
    $('.chatbot').click(function(){
        $('.chatbox').toggleClass('active');
    });

    // If there isn't already an input field and messages container inside the chatbox, add them.
    if ($('.chatbox').find('.messages').length === 0) {
        $('.chatbox').append('<div class="messages"></div><input type="text" id="chat-input" placeholder="Type your message...">');
    }

    // Listen for Enter key in the chat input field
    $('#chat-input').on('keypress', function(e){
        if(e.which === 13){
            let message = $(this).val().trim();
            if(message !== ''){
                // Append the user's message to the messages container
                $('.messages').append('<div class="user-message">'+message+'</div>');
                $(this).val('');

                // Send the message to your chatbot API endpoint
                $.ajax({
                    url: 'http://localhost:5000/chat', // Your chatbot backend endpoint
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ message: message }),
                    success: function(response){
                        // Append the bot's response to the messages container
                        $('.messages').append('<div class="bot-message">'+response.response+'</div>');
                        // Auto-scroll to the bottom
                        $('.messages').scrollTop($('.messages')[0].scrollHeight);
                    },
                    error: function(){
                        $('.messages').append('<div class="bot-message">Sorry, an error occurred.</div>');
                    }
                });
            }
        }
    });
});
