
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
                $('.saved-post-' + id).remove();
            }

        },
    });
}

let addFriendSubmit = (id,type) => {

    $.ajax({
        url: $('#'+type+'-friend-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById(type+"-friend-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#'+type+'-friend-btn-' + id).text(data.msg);
            if(data.msg == "add friend") {
                $('#'+type+'-request-type-' + id).attr('value', 'addFriendRequest')
                $('#sender-'+id).attr('name',data.sender);
                $('#receiver-'+id).attr('name',data.receiver);
            }
            else{
                $('#'+type+'-request-type-' + id).attr('value', 'removeFriendRequest')
            }
        },
        error: function (data) {
            console.log(data.responseText);
        }
    });
}

let friendRequestSubmit = (id,type) => {

    $.ajax({
        url: $('#'+type+'-friend-request-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById(type + "-friend-request-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            var div = document.getElementById('friend-request-div-'+id);
            div.innerHTML = data;
        },
        error: function (data) {
            console.log(data.responseText);
        }
    });
}


let addBlockSubmit = (id) => {

    $.ajax({
        url: $('#search-block-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("search-block-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#search-block-btn-' + id).text(data.msg);
            if(data.msg == "block") {
                $('#block-request-type-' + id).attr('value', 'addBlockRequest')
            }
            else{
                $('#block-request-type-' + id).attr('value', 'removeBlockRequest')
            }
        },
        error: function (data) {
            console.log(data.responseText);
        }
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
            if(data.msg == "join") {
                $('#join-flag-' + id).attr('value',0)
                if(parseInt($('#members-' + id).text()) > 0) {
                    $('#members-' + id).text(parseInt($('#members-' + id).text()) - 1 + ' members');
                }
            }
            else{
                $('#join-flag-' + id).attr('value',1)
                if(data.msg == "exit group") {
                    $('#members-' + id).text(parseInt($('#members-' + id).text()) + 1 + ' members');
                }
            }
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
            if(data.msg == "like") {
                $('#like-page-flag-' + id).attr('value',0)
                if(parseInt($('#page-members-' + id).text()) > 0) {
                    $('#page-members-' + id).text(parseInt($('#page-members-' + id).text()) - 1 + ' likes');
                }
            }
            else{
                $('#like-page-flag-' + id).attr('value',1)
                $('#page-members-' + id).text(parseInt($('#page-members-' + id).text()) + 1 + ' likes');
            }
        },
        error: function (data) {
            console.log(data.responseText);
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message').css('display','block');
            $('#error-status').text(errormsg.msg);
        }
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
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#add-post-modal').modal('hide');
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            var div = document.getElementById('addedpost');
            $('#progress-modal').modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("post created successfully");
            div.innerHTML = data + div.innerHTML;
        },
        error: function (data) {
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
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#edit-post-modal-'+ id).modal('hide');
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            var div = document.getElementById('post-' + id);
            $('#progress-modal').modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("post edited successfully");
            div.classList.remove('post-container','bg-white','mt-3','p-3');
            div.innerHTML = data;
        },
        error: function (data) {
            console.log(data.responseText);
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
            console.log(data.responseText)
            var errormsg = $.parseJSON(data.responseText);
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
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            $('#progress-modal').modal('hide');
            $('#img-div-comment-'+id).css('display','none');
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
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            $('#progress-modal').modal('hide');
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
            var div = document.getElementById(data.type+'-' + comment_id);
            // if(parseInt($('#comment-count-' + post_id).text()) > 0) {
            //     $('#comment-count-' + post_id).text(parseInt($('#comment-count-' + post_id).text()) - 1);
            // }
            // else {
            $('#comment-count-' + post_id).text(data.count);
            // }
            $('#success-modal').modal('show');
            $('#success-modal-message').text(data.msg);
            if(data.type == "comment") {
                $('#comment-' + comment_id).remove();
                $('#comment-hr' + comment_id).css('display', 'none');
            }
            else {
                $('#reply-' + comment_id).remove();
            }
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
            if(data.type == "comment") {
                $('#comment-' + id).remove();
                $('#comment-hr' + id).css('display', 'none');
            }
            else {
                $('#reply-' + id).remove();
            }
        },
        error: function (data) {
            console.log(data.responseText);
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}

let addReplySubmit = (id,post_id) => {

    $.ajax({
        url: $('#add-reply-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("add-reply-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            $('#progress-modal').modal('hide');
            $('#img-div-reply-'+id).css('display','none');
            var div = document.getElementById('added-reply-' + id);
            console.log(parseInt($('#comment-count-' + post_id).text()));
            if(parseInt($('#comment-count-' + post_id).text()) > 0) {
                $('#comment-count-' + post_id).text(parseInt($('#comment-count-' + post_id).text()) + 1);
            }
            else {
                $('#comment-count-' + post_id).text("1");
            }
            div.innerHTML = div.innerHTML + data;
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            console.log(errormsg)
        }
    });
}


let editReplySubmit = (id) => {

    $.ajax({
        url: $('#edit-reply-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("edit-reply-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            $('#progress-modal').modal('hide');
            var div = document.getElementById('reply-' + id);
            div.classList.remove('comment','d-flex','justify-content-between');
            div.innerHTML = data;
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            console.log(errormsg);
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
            console.log(data.responseText)
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}

let likeModelSubmit = (model_id,react_id) => {

    // Here we are getting the reaction which is tapped by using the data-reaction attribute defined in main page
    var data_reaction = $("#react-" + react_id).attr("data-reaction");
    // Sending Ajax request in handler page to perform the database operations
    $.ajax({
        type: "POST",
        url: $('#like-form-' + model_id + '-' + react_id).attr('action'),
        data: new FormData(document.getElementById("like-form-" + model_id + "-" + react_id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            var div = document.getElementById('reaction-container-' + model_id);
            div.innerHTML = response;
            console.log(response);
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            console.log(errormsg);
        }
    })
}


let unlikeModelSubmit = (model_id,react_id) => {

    if ($('#reaction-btn-text-' + model_id).hasClass("active")) {
            // Sending Ajax request in handler page to perform the database operations
        $.ajax({
            type: "POST",
            url: $('#unlike-form-' +  model_id + '-' + react_id).attr('action'),
            data: new FormData(document.getElementById("unlike-form-" + model_id + "-" + react_id)),
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                var div = document.getElementById('reaction-container-' + model_id);
                div.innerHTML = response;
                console.log(response);
            },
            error: function (data) {
                var errormsg = $.parseJSON(data.responseText);
                console.log(errormsg);
            }
        })
    }
}




let addStorySubmit = (publisher_id) => {

    $.ajax({
        url: $('#add-story-form').attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("add-story-form")),
        cache: false,
        processData: false,
        contentType: false,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#add-story-modal').modal('hide');
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            $('#progress-modal').modal('hide');
            $('#story-' + publisher_id).css('display','block');
            var div = document.getElementById('show-story-modal-' + publisher_id);
            $('#success-modal').modal('show');
            $('#success-modal-message').text("story added successfully");
            div.innerHTML = data;

        },
        error: function (data) {
            console.log(data.responseText);
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-story').css('display','block');
            $('#error-status-story').text(errormsg.msg);
        }
    });
}


let deleteStorySubmit = (id,publisher_id) => {

    $.ajax({
        url: $('#delete-story-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("delete-story-form-" + id)),
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#show-story-modal-' + publisher_id).modal('hide');
            $('#success-modal').modal('show');
            $('#success-modal-message').text(data.msg);
            $("#carousel-item-" + id).remove();
            $('#story-carousel-' + publisher_id).find('.carousel-item').first().addClass('active');
        },
        error: function (data) {
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + id).css('display','block');
            $('#error-status-' + id).text(errormsg.msg);
        }
    });
}


let addStoryViews = (id) => {

    $('.carousel-'+id).addClass('current');

    var active_id = $('div.carousel-item.active.carousel-'+id).attr('id');
    var story_id = active_id.split('-')[2];

    // if($('.carousel div.carousel-item.active.current').attr('data-type') == 'video') {

        var myEle = document.getElementById("story-video-" + story_id);
        if(myEle){
            $("#story-video-" + story_id)[0].play();
        }
    // }

    $.ajax({
        url: $('#view-story-form-' + story_id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("view-story-form-" + story_id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            console.log(data);
        },
        error: function (data) {
            console.log(data);
        }
    });
}

let removeCurrent = (id) => {
    $('.carousel-'+id).removeClass('current');
    $("video")[0].pause();
}

let addServiceSubmit = () => {

    $.ajax({
        url: $('#add-service-form').attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("add-service-form")),
        cache: false,
        processData: false,
        contentType: false,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#add-service-modal').modal('hide');
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            $('#progress-modal').modal('show');
            var div = document.getElementById('all-services');
            $('#success-modal').modal('show');
            $('#success-modal-message').text("service created successfully");
            div.innerHTML = data + div.innerHTML;
            $('#no-service').css('display','none');
            var added_div = document.getElementById('added-service');
            added_div.classList.add('service','card','m-2');
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
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('#edit-service-modal-'+ id).modal('hide');
                    $('#progress-modal').modal('show');
                    $('#status').html('<b> Uploading -> ' + (Math.round(percentComplete * 100)) + '% </b>');
                }
            }, false);
            return xhr;
        },
        success: function (data) {
            $('#progress-modal').modal('hide');
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

let textAreaChange = (id) => {
    $('#textarea-' + id).id = 'textarea-edit';
}

let applySelect2 = () => {
    $('.js-example-basic-multiple').select2();
    $('.js-example-basic-single').select2();
}


let sponsorPost = (post_id) => {

    $.ajax({
        url: $('#sponsor-post-form-' + post_id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById('sponsor-post-form-' + post_id)),
        dataType : 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#advertise-post-modal-' + post_id).modal('hide');
            $('#payment-modal-' + post_id).modal('show');
            $('#payment-text-box-' + post_id).val(data.total_price);
        },
        error: function (data) {
            console.log(data.responseText);
            var errormsg = $.parseJSON(data.responseText);
            $('#error-message-' + post_id).css('display','block');
            $('#error-status-' + post_id).text(errormsg.msg);
        }
    });
}

let getPrice = (post_id) => {

    var time_id = 'time-' + post_id;

    var reach_id = 'reach-' + post_id;

    var reach_price = parseInt($("#time-"+ post_id +":checked").attr('data-value'));

    var time_price = parseInt($("#reach-"+ post_id +":checked").attr('data-value'));

    total_price = reach_price + time_price;

    $('#sponsored-post-price-' + post_id).val(total_price)
}

let loadComments = (post_id) => {

    var limit = 5;
    var start =  parseInt($('#load-comments-' + post_id).attr('data-value'));
    var action = 'active';
    function loadData(limit, start)
    {
        $.ajax({
            url:"loadcomments/"+post_id+'/'+limit+'/'+start,
            type: 'GET',
            cache: false,
            processData: false,
            contentType: false,
            success:function(data)
            {
                console.log(data)
                $('#load-comments-' + post_id).append(data);

                var myElement = document.getElementById("stop-load-comments-message-" + post_id);

                if(!myElement){
                    $('#load-comments-message-' + post_id).text('load more comments')
                    $('#load-comments-' + post_id).attr('data-value',start + limit)
                    action = "active";
                }
                else {
                    $('#load-comments-message-' + post_id).remove();
                }
            },
            error: function (data) {
                console.log(data.responseText);
            }
        });
    }

    if(action == 'active')
    {
        action = 'inactive';
        loadData(limit, start);
    }
}


let searchCompaniesSubmit = () => {

    var value = $('#search-companies').val();

    if(document.getElementById("search-companies").value.length == 0){
        value = null;
    }

    console.log(value);

    $.ajax({
        url:"search/companies/"+value,
        type: 'GET',
        cache: false,
        processData: false,
        contentType: false,
        success:function(data)
        {
            $('#load-companies').html(data);
        },
        error: function (data) {
            console.log(data.responseText);
        }
    });

}


let searchCategoriesSubmit = () => {

    var value = $('#search-categories').val();

    if(document.getElementById("search-categories").value.length == 0){
        value = null;
    }

    $.ajax({
        url:"search/categories/"+value,
        type: 'GET',
        cache: false,
        processData: false,
        contentType: false,
        success:function(data)
        {
            console.log(data);
            $('#load-categories').html(data);
        },
        error: function (data) {
            console.log(data.responseText);
        }
    });

}


let deleteGroupSubmit = (id) => {

    $.ajax({
        url: $('#delete-group-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("delete-group-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#searched-group-' + id).remove();
        },
        error: function (data) {
            console.log(data.responseText);
        }
    });
}

let deletePageSubmit = (id) => {

    $.ajax({
        url: $('#delete-page-form-' + id).attr('action'),
        type: 'POST',
        data: new FormData(document.getElementById("delete-page-form-" + id)),
        dataType: 'JSON',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#searched-page-' + id).remove();
        },
        error: function (data) {
            console.log(data.responseText);
        }
    });
}


let readURL = (id,input,type) => {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {

            $('#img-div-'+type+'-'+id).css('display','block');
            $('#img-'+type+'-'+id)
                .attr('src', e.target.result)
                .width(150)
                .height(200);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

let homeSearchSubmit = () => {

    var value = $('#home-search').val();
    var type = $('#search_type').val();

    if(document.getElementById("home-search").value.length == 0){
        value = null;
    }

    $.ajax({
        url:"search/"+type+'/'+value,
        type: 'GET',
        cache: false,
        processData: false,
        contentType: false,
        success:function(data)
        {
            console.log(data)
            if(value != null){
                $('#search-loading').css('display','none');
                $('#show-stories').css('display','none');
                $('#add-post').css('display','none');
            }
            else{
                $('#search-loading').css('display','block');
                $('#show-stories').css('display','block');
                $('#add-post').css('display','block');
            }
            $('#home-search-results').html(data);
        },
        error: function (data) {
            console.log(data.responseText);
        }
    });
}



let addServiceCities = (input) => {

    var country_id= input.value
    if (country_id) {
        $.ajax({
            url: "getcities/"+country_id,
            type: "GET",
            cache: false,
            processData: false,
            contentType: false,
            success: function(data){
                $('.select-city').empty();
                $('.select-city').html(data);
                $('.select-city').prop('disabled',false);
            },
            error: function (data) {
                console.log(data.responseText);
            }
        });
    }else {
        $('select[name="city_id"]').empty();
    }
}


let editServiceCities = (input,id) => {

    var country_id= input.value
    if (country_id) {
        $.ajax({
            url: "getcities/"+country_id,
            type: "GET",
            cache: false,
            processData: false,
            contentType: false,
            success: function(data){
                $('.edit-city-' + id).empty();
                $('.edit-city-' + id).html(data);
                $('.edit-city-' + id).prop('disabled',false);
            },
            error: function (data) {
                console.log(data.responseText);
            }
        });
    }else {
        $('select[name="city_id"]').empty();
    }
}

const properties = [
    'direction',
    'boxSizing',
    'width',
    'height',
    'overflowX',
    'overflowY',

    'borderTopWidth',
    'borderRightWidth',
    'borderBottomWidth',
    'borderLeftWidth',
    'borderStyle',

    'paddingTop',
    'paddingRight',
    'paddingBottom',
    'paddingLeft',

    'fontStyle',
    'fontVariant',
    'fontWeight',
    'fontStretch',
    'fontSize',
    'fontSizeAdjust',
    'lineHeight',
    'fontFamily',

    'textAlign',
    'textTransform',
    'textIndent',
    'textDecoration',

    'letterSpacing',
    'wordSpacing',

    'tabSize',
    'MozTabSize',
]

const isFirefox = typeof window !== 'undefined' && window['mozInnerScreenX'] != null

/**
 * @param {HTMLTextAreaElement} element
 * @param {number} position
 */
function getCaretCoordinates(element, position) {
    const div = document.createElement('div')
    document.body.appendChild(div)

    const style = div.style
    const computed = getComputedStyle(element)

    style.whiteSpace = 'pre-wrap'
    style.wordWrap = 'break-word'
    style.position = 'absolute'
    style.visibility = 'hidden'

    properties.forEach(prop => {
        style[prop] = computed[prop]
    })

    if (isFirefox) {
        if (element.scrollHeight > parseInt(computed.height))
            style.overflowY = 'scroll'
    } else {
        style.overflow = 'hidden'
    }

    div.textContent = element.value.substring(0, position)

    const span = document.createElement('span')
    span.textContent = element.value.substring(position) || '.'
    span.className = "item"
    div.appendChild(span)

    const coordinates = {
        top: span.offsetTop + parseInt(computed['borderTopWidth']),
        left: span.offsetLeft + parseInt(computed['borderLeftWidth']),
        // height: parseInt(computed['lineHeight'])
        height: span.offsetHeight
    }

    div.remove()

    return coordinates
}

class Mentionify {
    constructor(ref, menuRef, resolveFn, replaceFn, menuItemFn) {
        this.ref = ref
        this.menuRef = menuRef
        this.resolveFn = resolveFn
        this.replaceFn = replaceFn
        this.menuItemFn = menuItemFn
        this.options = []

        this.makeOptions = this.makeOptions.bind(this)
        this.closeMenu = this.closeMenu.bind(this)
        this.selectItem = this.selectItem.bind(this)
        this.onInput = this.onInput.bind(this)
        this.onKeyDown = this.onKeyDown.bind(this)
        this.renderMenu = this.renderMenu.bind(this)

        this.ref.addEventListener('input', this.onInput)
        this.ref.addEventListener('keydown', this.onKeyDown)
    }

    async makeOptions(query) {
        const options = await this.resolveFn(query)
        if (options.length !== 0) {
            this.options = Array.from(options);
            this.renderMenu()
        } else {
            this.closeMenu()
        }
    }

    closeMenu() {
        setTimeout(() => {
            this.options = []
            this.left = undefined
            this.top = undefined
            this.triggerIdx = undefined
            this.renderMenu()
        }, 0)
    }

    selectItem(active) {
        return () => {
            const preMention = this.ref.value.substr(0, this.triggerIdx)
            const option = this.options[active]
            const mention = this.replaceFn(option, this.ref.value[this.triggerIdx])
            const postMention = this.ref.value.substr(this.ref.selectionStart)
            const newValue = `${preMention}${mention}${postMention}`
            this.ref.value = newValue
            const caretPosition = this.ref.value.length - postMention.length
            this.ref.setSelectionRange(caretPosition, caretPosition)
            this.closeMenu()
            this.ref.focus()
        }
    }

    onInput(ev) {
        const positionIndex = this.ref.selectionStart
        const textBeforeCaret = this.ref.value.slice(0, positionIndex)
        const tokens = textBeforeCaret.split(/\s/)
        const lastToken = tokens[tokens.length - 1]
        const triggerIdx = textBeforeCaret.endsWith(lastToken)
            ? textBeforeCaret.length - lastToken.length
            : -1
        const maybeTrigger = textBeforeCaret[triggerIdx]
        const keystrokeTriggered = maybeTrigger === '@'

        if (!keystrokeTriggered) {
            this.closeMenu()
            return
        }

        const query = textBeforeCaret.slice(triggerIdx + 1)
        this.makeOptions(query)

        const coords = getCaretCoordinates(this.ref, positionIndex)
        const { top, left } = this.ref.getBoundingClientRect()

        setTimeout(() => {
            this.active = 0
            this.left = window.scrollX  + coords.left + left + this.ref.scrollLeft
            this.top = window.scrollY +  coords.top + top + coords.height - this.ref.scrollTop
            this.triggerIdx = triggerIdx
            this.renderMenu()
        }, 0)
    }

    onKeyDown(ev) {
        let keyCaught = false
        if (this.triggerIdx !== undefined) {
            switch (ev.key) {
                case 'ArrowDown':
                    this.active = Math.min(this.active + 1, this.options.length - 1)
                    this.renderMenu()
                    keyCaught = true
                    break
                case 'ArrowUp':
                    this.active = Math.max(this.active - 1, 0)
                    this.renderMenu()
                    keyCaught = true
                    break
                case 'Enter':
                case 'Tab':
                    this.selectItem(this.active)()
                    keyCaught = true
                    break
            }
        }

        if (keyCaught) {
            ev.preventDefault()
        }
    }

    renderMenu() {
        if (this.top === undefined) {
            this.menuRef.hidden = true
            return
        }

        this.menuRef.innerHTML = ''

        this.options.forEach((option, idx) => {
            this.menuRef.appendChild(this.menuItemFn(
                option,
                this.selectItem(idx),
                this.active === idx))
        })

        this.menuRef.hidden = false
    }
}

const users = JSON.parse(window.users.user);

const resolveFn = prefix => prefix === ''
    ? users
    : users.filter(user => user.username.startsWith(prefix))

const replaceFn = (user, trigger) => `${trigger}${user.username} `

const menuItemFn = (user, setItem, selected) => {
    const div = document.createElement('div')
    var image = null;
    div.setAttribute('role', 'option')
    div.className = 'item-'+user.id
    div.classList.add('menu-item')
    if (selected) {
        div.classList.add('selected')
        div.setAttribute('aria-selected', '')
    }
    div.onclick = setItem

    if(user.image != null){
        image = 'http://127.0.0.1:8000/media/'+user.image;
    }
    else {
        image = "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80";
    }

    div.innerHTML = '                                                                            <div class="people-info d-flex align-items-center">\n' +
        '                                                                                <img style="width: 45px; height: 45px" class="profile-figure rounded-circle"\n' +
        '                                                                                         src="'+image+'"\n' +
        '                                                                                         alt="User Profile Pic">\n' +
        '                                                                                <p class="mb-0 ml-3"><b>'+user.username+'</b></p>\n' +
        '                                                                            </div>'
    return div
}


let mentionAdd = (text_area_id,menu_id) => {

    new Mentionify(
        document.getElementById(text_area_id),
        document.getElementById(menu_id),
        resolveFn,
        replaceFn,
        menuItemFn
    )
}





