
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