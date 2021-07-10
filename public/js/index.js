$(document).ready(function () {
    $(".filter-button").click(function () {
        var value = $(this).attr("data-filter");
        if (value == "all") $(".filter").show("1000");
        else {
            $(".filter")
                .not("." + value)
                .hide("3000");
            $(".filter")
                .filter("." + value)
                .show("3000");
        }
        $(".filter-button").removeClass("ez-active")
        $(this).addClass("ez-active");
    });
    if ($(".filter-button").removeClass("active"))
        $(this).removeClass("active");
    $(this).addClass("active");
});

let openSidenav = () => {
    $('#sideNavbar').css('width', '100%')
    $('#sideNavbar').css('display', 'block')
    $('.navbar').css('position', 'fixed')
}

let closeSidenav = () => {
    $('#sideNavbar').css('display', 'none')
    $('.navbar').css('position', 'static')
}

let commentAttach = () => {
    $("#file1").trigger('click');
}

$("#post-type-service").click(() => $("#post-type-service-content").removeClass("d-none"))
$("#post-type-post").click(() => $("#post-type-service-content").addClass("d-none"))
