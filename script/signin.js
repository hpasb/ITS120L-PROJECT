$(document).ready(function() {
    $(".signin").click(function(event) {
        event.preventDefault(); // 🔥 Prevents page refresh

        let userName = $("#username").val(); // Get input value
        let password = $("#password").val();

        console.log("Signin button clicked!");
        console.log("Entered Username:", userName);
        console.log("Entered Password:", password);

        if (userName === "admin" && password === "admin") {
            console.log("Redirecting to admin page...");
            window.location.href = "pages/admin.html";
        } else {
            console.log("Redirecting to home page...");
            window.location.href = "pages/home.html";
        }
    })

    $(".signup").click(function () {
        window.location.href="./pages/signup.html"
    })

    ;
});
