$(document).ready(function() {
    $("#menu-icon").click(function() {
        let sideNav = $(".side-nav");
        let menuIcon = $(this);

        if (sideNav.css("display") === "none") {
            sideNav.css("display", "flex").hide().fadeIn(300); 
            menuIcon.attr("src", "../assets/sidebar/ep_back.png");
        } else {
            sideNav.fadeOut(300, function() {
                sideNav.css("display", "none");
                menuIcon.attr("src", "../assets/sidebar/material-symbols_menu-rounded.png");
            });
        }
    });
});
