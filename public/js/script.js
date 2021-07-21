
let savePostSubmit = (id) => {

    $.ajax({
        url: $('#save-post-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("save-post-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#save-post-' + id).text(data.msg);
            if(data.msg == "saved"){
                $('#save-post-flag-' + id).attr('value',1);
            }
            else{
                $('#save-post-flag-' + id).attr('value',0);
            }

        },
    });
}

let addFriendSubmit = (id) => {

    $.ajax({
        url: $('#friend-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("friend-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#friend-btn-' + id).text(data.msg);
            $('#request-type-' + id).attr('value','removeFriendRequest')
        },
    });
}


let joinGroupSubmit = (id) => {

    $.ajax({
        url: $('#join-group-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("join-group-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            console.log(data.msg);
            $('#join-btn-' + id).text(data.msg);
            $('#join-flag-' + id).attr('value',1)
        },
        error: function (data) {
            console.log(data);
        }
    });
}


let likePageSubmit = (id) => {

    $.ajax({
        url: $('#like-page-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("like-page-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#like-page-btn-' + id).text(data.msg);
            $('#like-page-flag-' + id).attr('value',1)
        },
    });
}


let addPostSubmit = () => {

    $.ajax({
        url: $('#add-post-form').attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("add-post-form")),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('addedpost');
            $('#addedstory').css('display','inline-block');
            $('#add-post-modal').modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("post created successfully");
            div.innerHTML = data + div.innerHTML;
        },
        error: function (data) {
            console.log(data.responseText);
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message').css('display','block');
            $('#error-status').text(errormsg.msg);
        }
    });
}

let editPostSubmit = (id) => {

    $.ajax({
        url: $('#edit-post-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("edit-post-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('post-' + id);
            $('#edit-post-modal-'+ id).modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("post edited successfully");
            div.classList.remove('post-container','bg-white','mt-3','p-3');
            div.innerHTML = data;
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}


let deletePostSubmit = (id) => {

    $.ajax({
        url: $('#delete-post-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("delete-post-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#success-modal').modal('show');
            $('#success-modal-message').text(data.msg);
            $('#post-' + id).css('display','none')
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}

let reportPostSubmit = (id) => {

    $.ajax({
        url: $('#report-post-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("report-post-form-" + id)),
        dataType: "JSON",
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#success-modal').modal('show');
            $('#success-modal-message').text(data.msg);
            $('#post-' + id).css('display','none')
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            console.log(errormsg);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}

let addCommentSubmit = (id) => {

    $.ajax({
        url: $('#add-comment-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("add-comment-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('added-comment-' + id);
            if(parseInt($('#comment-count-' + id).text()) > 0) {
                $('#comment-count-' + id).text(parseInt($('#comment-count-' + id).text()) + 1);
            }
            else {
                $('#comment-count-' + id).text("1");
            }
            div.innerHTML = div.innerHTML + data;
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            console.log(errormsg)
        }
    });
}

let editCommentSubmit = (id) => {

    $.ajax({
        url: $('#edit-comment-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("edit-comment-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('comment-' + id);
            div.classList.remove('comment','d-flex','justify-content-between');
            div.innerHTML = data;
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            console.log(errormsg);
        }
    });
}

let deleteCommentSubmit = (comment_id,post_id) => {

    $.ajax({
        url: $('#delete-comment-form-' + comment_id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("delete-comment-form-" + comment_id)),
        dataType: "JSON",
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('comment-' + comment_id);
            if(parseInt($('#comment-count-' + post_id).text()) > 0) {
                $('#comment-count-' + post_id).text(parseInt($('#comment-count-' + post_id).text()) - 1);
            }
            else {
                $('#comment-count-' + post_id).text("1");
            }
            $('#success-modal').modal('show');
            $('#success-modal-message').text(data.msg);
            div.classList.remove('comment','d-flex','justify-content-between');
            $('#comment-' + comment_id).css('display','none');
            $('#comment-hr' + comment_id).css('display','none');
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}

let reportCommentSubmit = (id) => {

    $.ajax({
        url: $('#report-comment-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("report-comment-form-" + id)),
        dataType: "JSON",
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('comment-' + id);
            $('#success-modal').modal('show');
            $('#success-modal-message').text(data.msg);
            div.classList.remove('comment','d-flex','justify-content-between');
            $('#comment-' + id).css('display','none');
            $('#comment-hr' + id).css('display','none');
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            console.log(errormsg);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}



let sharePostSubmit = (id) => {

    $.ajax({
        url: $('#share-post-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("share-post-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('addedpost');
            $('#share-post-modal-' + id).modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("post shared to your timeline");
            div.innerHTML = data + div.innerHTML;
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}

let likePostSubmit = (post_id,react_id) => {

    // Here we are getting the reaction which is tapped by using the data-reaction attribute defined in main page
    var data_reaction = $("#react-" + react_id).attr("data-reaction");
    // Sending Ajax request in handler page to perform the database operations
    $.ajax({
        type: "POST",
        url: $('#like-form-' + post_id + '-' + react_id).attr('action'),
        data: new FormData(document.getElementById("like-form-" + post_id + "-" + react_id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            // // This code will run after the Ajax is successful
            // if(response.update == false){
            //     $("#like-details-" + post_id).html("You"+$("#like-details-" + post_id).text());
            // }
            // $("#reaction-btn-emo-" + post_id).css('display','inline-block');
            // $("#like-stat-" + post_id).css('display','block');
            // $("#reaction-btn-emo-" + post_id).removeClass().addClass('reaction-btn-emo').addClass('like-btn-' + data_reaction);
            // $("#reaction-btn-text-" + post_id).text(data_reaction).removeClass().addClass('reaction-btn-text').addClass('reaction-btn-text-' + data_reaction).addClass("active");
            //
            // if (data_reaction == "like")
            //     $("#like-emo-" + post_id).html('<span class="like-btn-like"></span>');
            // else
            //     $("#like-emo-" + post_id).html('<span class="like-btn-like"></span><span class="like-btn-' + data_reaction + '"></span>');

            var div = document.getElementById('reaction-container-' + post_id);
            div.innerHTML = response;
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            console.log(errormsg);
        }
    })
}


let unlikePostSubmit = (post_id,react_id) => {

    if ($('#reaction-btn-text-' + post_id).hasClass("active")) {
            // Sending Ajax request in handler page to perform the database operations
        $.ajax({
            type: "POST",
            url: $('#unlike-form-' + post_id + '-' + react_id).attr('action'),
            data: new FormData(document.getElementById("unlike-form-" + post_id + "-" + react_id)),
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                var div = document.getElementById('reaction-container-' + post_id);
                div.innerHTML = response;
            },
            error: function (data) {
                var errormsg = $.parseJSON(data.responseText);
                console.log(errormsg);
            }
        })
    }
}




let addStorySubmit = () => {

    $.ajax({
        url: $('#add-story-form').attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("add-story-form")),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('addedstory');
            $('#addedstory').css('display','inline-block');
            $('#add-story-modal').modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("story added successfully");
            div.innerHTML = data + div.innerHTML;

        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-story').css('display','block');
            $('#error-status-story').text(errormsg.msg);
        }
    });
}


let deleteStorySubmit = (id) => {

    $.ajax({
        url: $('#delete-story-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("delete-story-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#show-story-modal-' + id).modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text(data.msg);
            $('#story-' + id).css('display','none')
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}


let addStoryViews = (id) => {

    $.ajax({
        url: $('#view-story-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("view-story-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            console.log(data.msg);
        },
        error: function (data) {
            console.log('dsff');
        }
    });
}

let addServiceSubmit = () => {

    $.ajax({
        url: $('#add-service-form').attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("add-service-form")),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('addedservice');
            $('#add-service-modal').modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("service created successfully");
            div.innerHTML = data + div.innerHTML;
            $('#added-service-div').addClass('service card m-2');
            $('#no-service').css('display','none');
        },
        error: function (data) {
            console.log(data.responseText);
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message').css('display','block');
            $('#error-status').text(errormsg.msg);
        }
    });
}

let editServiceSubmit = (id) => {

    $.ajax({
        url: $('#edit-service-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("edit-service-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#edit-service-modal-'+ id).modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("service edited successfully");
            $('.service-id-' + id).html(data);
        },
        error: function (data) {
            console.log(data.responseText);
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}


let deleteServiceSubmit = (id) => {

    $.ajax({
        url: $('#delete-service-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("delete-service-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#service-modal-' + id).modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text(data.msg);
            $('.service-id-' + id).css('display','none')
        },
        error: function (data) {
            console.log(data.responseText);
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}

$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
