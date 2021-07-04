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