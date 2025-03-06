$(document).ready(function() {
    $("#chatbot").click(function(event) {
        let chatbox = $(".chatbox");

        if (chatbox.css("display") === "none") {
            chatbox.css("display", "block");
        } else {
            chatbox.css("display", "none");
        }

        event.stopPropagation();
    });

    $(document).click(function(event) {
        let chatbox = $(".chatbox");

        if (!chatbox.is(event.target) && chatbox.has(event.target).length === 0 && !$("#chatbot").is(event.target)) {
            chatbox.css("display", "none");
        }
    });
});
