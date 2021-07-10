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


let toggleCommentOptions = (id) => {
    if ($('.comment-options-' + id).css('display') == 'block') $('.comment-options-' + id).css('display', 'none')
    else $('.comment-options-' + id).css('display', 'block')
}

let toggleOptions = (id) => {
    if ($('.post-options-' + id).css('display') == 'block') $('.post-options-' + id).css('display', 'none')
    else $('.post-options-' + id).css('display', 'block')
}

let toggleComments = (id) => {
    if ($('.post-comment-list-' + id).css('display') == 'block') $('.post-comment-list-' + id).css('display', 'none')
    else $('.post-comment-list-' + id).css('display', 'block')
}
let commentAttachClick = (id) => {
    $("#comment-attach-" + id).trigger('click');
}

let deletePost = (id) => { $('#post-' + id).css('display', 'none') }
