$(function() {

    fetchRecords();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#friend_btn").on('click', function (e) {

        e.preventDefault();
        var el = $(this);

        $.ajax({
            url: el.attr('href'),
            type: 'POST',
            data: { value: el.attr('data-id')},
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#friend_btn').text(data.message);
            },
        });
    });

    $("#friend-form").on('submit', function (e) {

        e.preventDefault();

        $.ajax({
            url: "addfriend",
            type: 'POST',
            data: new FormData(this),
            dataType: 'JSON',
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#friend_btn').text(data.message);
            },
        });

    });


    $("#join_btn").on('click', function (e) {
        e.preventDefault();

        $("#join-form").submit();
    });

    $("#join-form").on('submit', function (e) {

        e.preventDefault();

        $.ajax({
            url: "/joingroup",
            type: 'POST',
            data: new FormData(this),
            dataType: 'JSON',
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log('sdfsf')
                $('#friend').text('request sent');
                // $('#message').attr('hidden','false');
                // $('#uploaded_image').html(data.uploaded_image);
            },
        });

    });

    $("#like_btn").on('click', function (e) {
        e.preventDefault();

        $("#like-form").submit();
    });

    $("#like-form").on('submit', function (e) {

        e.preventDefault();

        $.ajax({
            url: "/likepage",
            type: 'POST',
            data: new FormData(this),
            dataType: 'JSON',
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log('sdfsf')
                $('#friend').text('request sent');
                // $('#message').attr('hidden','false');
                // $('#uploaded_image').html(data.uploaded_image);
            },
        });

    });

    function fetchRecords() {
        $.ajax({
            url: 'home',
            type: 'get',
            dataType: 'json',
            success: function (response) {

                var len = 0;
                $('#userTable tbody tr:not(:first)').empty(); // Empty <tbody>
                if (response['data'] != null) {
                    len = response['data'].length;
                }

                if (len > 0) {
                    for (var i = 0; i < len; i++) {

                        var id = response['data'][i].id;
                        var username = response['data'][i].username;
                        var name = response['data'][i].name;
                        var email = response['data'][i].email;

                        var tr_str = "<tr>" +
                            "<td align='center'><input type='text' value='" + username + "' id='username_" + id + "' disabled></td>" +
                            "<td align='center'><input type='text' value='" + name + "' id='name_" + id + "'></td>" +
                            "<td align='center'><input type='email' value='" + email + "' id='email_" + id + "'></td>" +
                            "<td align='center'><input type='button' value='Update' class='update' data-id='" + id + "' ><input type='button' value='Delete' class='delete' data-id='" + id + "' ></td>" +
                            "</tr>";

                        $("#userTable tbody").append(tr_str);

                    }
                } else {
                    var tr_str = "<tr class='norecord'>" +
                        "<td align='center' colspan='4'>No record found.</td>" +
                        "</tr>";

                    $("#userTable tbody").append(tr_str);
                }

            }
        });
    }

});


