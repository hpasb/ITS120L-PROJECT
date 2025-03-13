$(document).ready(function() {
    $(".user").click(function() {
        window.location.href = "../pages/user_profile.php";
    });
});
$(document).ready(function() {
    $(".card").click(function() {
        window.location.href = "../pages/pet_profile.php";
    });
});

$(document).ready(function() {
    $(".back").click(function() {
        window.history.back();
    });
});






document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("changePasswordModal");
    const changePasswordBtn = document.querySelector(".change-password-btn");
    const closeBtn = document.querySelector(".close");

    changePasswordBtn.addEventListener("click", function () {
        modal.style.display = "flex";
    });

    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
