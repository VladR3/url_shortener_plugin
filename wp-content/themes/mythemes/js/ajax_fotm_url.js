jQuery(document).ready(function () {

    function show_data() {
        var tableBody = $('#tbody');
        $.ajax({
            url: ajaxurl,
            method: "GET",
            dataType: "json",
            data: {
                action: 'show_data',
            },
            success: function (data) {
                tableBody.empty();
                if (data) {
                    url = data;
                } else {
                    url = "";
                }
                for (var shortUrl in data) {
                    if (data.hasOwnProperty(shortUrl)) {
                        var originalUrl = data[shortUrl]['original_url'];
                        var row = $('<tr></tr>').addClass('d-flex flex-wrap align-items-center');
                        var shortUrlCell = $('<td></td>').addClass('col-12 col-md-4 text-break').text(shortUrl);
                        var originalUrlCell = $('<td></td>').addClass('col-12 col-md-4 text-break').text(originalUrl);
                        var actionCell = $('<td></td>').addClass('col-12 col-md-4 d-flex justify-content-md-end').html(
                            '<a class="btn btn-edit me-2" data-bs-toggle="modal" data-bs-target="#updateModal" data-sid=' + data[shortUrl]['post_id'] + '><i class="bi bi-pencil"></i></a>' +
                            '<a class="btn btn-del" data-bs-toggle="modal" data-bs-target="#delConfirmModal" data-sid=' + data[shortUrl]['post_id'] + '><i class="bi bi-trash"></i></a>'
                        );
                
                        row.append(shortUrlCell, originalUrlCell, actionCell);
                        tableBody.append(row);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("Статус помилки: " + status);
                console.error("Помилка: " + error);
                console.error("Відповідь сервера: " + xhr.responseText);
                alert('Виникла помилка при завантаженні даних.');
            }
        });
    }

    $('#save-button').on('click', function (e) {
        e.preventDefault();

        if ($('#original_url').val() === "") {
            let message = "Enter url !";
            $('.message').text(message);
            show_message(5000);
            return;
        }
        $('.message').text("");
        var original_url = $('#original_url').val();
        $('#original_url').val('');
        console.log("Перед відправкою AJAX-запиту");
        console.log("ajaxurl: " + ajaxurl);
        console.log("original_url: " + original_url);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'save_data_urls',
                original_url: original_url,
            },
            success: function (response) {
                console.log("Відповідь AJAX:");
                console.log(response);
                show_data();
                if (response.success) {

                } else {
                    alert('Помилка: ' + response.data.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Статус помилки: " + status);
                console.error("Помилка: " + error);
                console.error("Відповідь сервера: " + xhr.responseText);
                alert('Виникла помилка при збереженні даних.');
            }
        });
    });

    show_data();

    $('#tbody').on('click', '.btn-edit', function () {
        let post_id = $(this).data('sid');
        $('#post_id').val(post_id);
        $('#new_original_url').val($(this).closest('tr').find('td:nth-child(2)').text());
        $('#short_url').val($(this).closest('tr').find('td:nth-child(1)').text());
        $('.modal_massage').text("");
    });

    $('#edit-save-button').on('click', function () {
        let postId = $('#post_id').val();
        let originalUrl = $('#new_original_url').val();
        let shortUrl = $('#short_url').val();


        console.log("Post ID:", postId);
        console.log("Original URL:", originalUrl);
        console.log("Short URL:", shortUrl);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_url',
                post_id: postId,
                new_original_url: originalUrl,
                new_short_url: shortUrl
            },
            success: function (response) {
                if (response.success) {
                    $('.modal_massage').text('URL updated successfully');
                    show_message(5000);
                    show_data();

                } else {
                    console.log(response.data);
                    $('.modal_massage').text(response.data.join(" "));
                    show_message(5000, '.modal_massage');
                }
            },
            error: function (xhr, status, error) {
                console.error("Статус помилки: " + status);
                console.error("Помилка: " + error);
                console.error("Відповідь сервера: " + xhr.responseText);
                $('.message').text('Виникла помилка при оновленні даних.');
            }
        });
    });

    $('#tbody').on('click', '.btn-del', function () {
        let post_id = $(this).data('sid');
        $('#post_id_for_del').val(post_id);
    });

    $('#del-confirm-button').on('click', function () {
        let post_id = $('#post_id_for_del').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'del_data_url',
                post_id: post_id
            },
            success: function (response) {
                show_data();
            }
        });
        

        
    });

    function show_message(duration, element_class = '.message') {
        setTimeout(function () {
            $(element_class).empty();
        }, duration);
    }

});

