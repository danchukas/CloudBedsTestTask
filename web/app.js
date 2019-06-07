function reloadIntervals() {
    $.ajax({
        url: 'http://localhost:8069/getFullPriceList',             // указываем URL и
        dataType: "json",                     // тип загружаемых данных
        success: function (data, textStatus) { // вешаем свой обработчик на функцию success
            var intervals = '';
            $.each(data, function (index, val) {    // обрабатываем полученные данные
                intervals +=
                    "<tr>" +
                    "<td>" + val.id + "</td>" +
                    "<td>" + val.dateStart + "</td>" +
                    "<td>" + val.dateEnd + "</td>" +
                    "<td>" + val.price + "</td>" +
                    "<td><button type='button' class='delete'>Delete</button></td>>" +
                    "</tr>"
                ;
            });
            $("tbody").html(intervals);
        }
    });
}

$(document).ready(function() {


    reloadIntervals();

    $(document).on('click', '.delete', function () {
        var row = $(this.parentElement.parentElement);
        $.ajax({
            url: 'http://localhost:8069/deleteInterval',             // указываем URL и
            method: 'POST',
            data: {
                id: row.find('td')[0].textContent,
                date_start: row.find('td')[1].textContent,
                date_end: row.find('td')[2].textContent,
                price: row.find('td')[3].textContent,
            },
            dataType : "json",                     // тип загружаемых данных
            complete: function () { // вешаем свой обработчик на функцию success
                reloadIntervals();
            }
        });

    });


    $('form').on('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var form = $(this);
        $.ajax({
            url: 'http://localhost:8069/addInterval',             // указываем URL и
            method: 'POST',
            data: {
                date_start: form.find('input')[0].value,
                date_end: form.find('input')[1].value,
                price: form.find('input')[2].value,
            },
            dataType: "json",                     // тип загружаемых данных
            complete: function () { // вешаем свой обработчик на функцию success
                reloadIntervals();
            }
        });

        return false;
    });


    // $('#show-add').click(function() {
    //     $('#link-add').slideDown(500);
    //     $('#show-add').hide();
    // });
    //
    // $('#add').click(function() {
    //     var name = $('#name').val();
    //     var username = $('#username').val();
    //     var password = $('#password').val();
    //
    //     $.ajax({
    //         url: "http://localhost:8069/addInterval",
    //         type: "POST",
    //         data: { date_start: name, date_end: username, price: password },
    //         success: function(data, status, xhr) {
    //             $('#name').val('');
    //             $('#username').val('');
    //             $('#password').val('');
    //             $.get("http://localhost:8069/getFullPriceList", function(html) {
    //                 $("#table_content").html(html);
    //             });
    //             $('#records_content').fadeOut(1100).html(data);
    //         },
    //         error: function() {
    //             $('#records_content').fadeIn(3000).html('<div class="text-center">error here</div>');
    //         },
    //         beforeSend: function() {
    //             $('#records_content').fadeOut(700).html('<div class="text-center">Loading...</div>');
    //         },
    //         complete: function() {
    //             $('#link-add').hide();
    //             $('#show-add').show(700);
    //         }
    //     });
    // });
});