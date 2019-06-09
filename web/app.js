const api_address = 'http://localhost:8069';

function reloadIntervals() {
    $.ajax({
        url: api_address + '/getFullPriceList',             // указываем URL и
        dataType: "json",                     // тип загружаемых данных
        success: function (data) { // вешаем свой обработчик на функцию success
            var intervals = '';
            $.each(data, function (index, val) {    // обрабатываем полученные данные
                intervals +=
                    "<tr>" +
                    "<td><input type=\"hidden\" name=\"current_id\" value=\"" + val.id + "\"/><input type=\"number\" name=\"new_id\" value=\"" + val.id + "\"/></td>" +
                    "<td><input type=\"hidden\" name=\"current_date_start\" value=\"" + val.dateStart + "\"/><input type=\"date\" name=\"new_date_start\" value=\"" + val.dateStart + "\"/></td>" +
                    "<td><input type=\"hidden\" name=\"current_date_end\" value=\"" + val.dateEnd + "\"/><input type=\"date\" name=\"new_date_end\" value=\"" + val.dateEnd + "\"/></td>" +
                    "<td><input type=\"hidden\" name=\"current_price\" value=\"" + val.price + "\"/><input name=\"new_price\" value=\"" + val.price + "\"/></td>" +
                    "<td><button type='button' class='update'>Update</button></td>" +
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
            url: api_address + '/deleteInterval',             // указываем URL и
            method: 'POST',
            data: {
                id: row.find('td:eq(0)').find('input')[0].value,
                date_start: row.find('td:eq(1)').find('input')[0].value,
                date_end: row.find('td:eq(2)').find('input')[0].value,
                price: row.find('td:eq(3)').find('input')[0].value,
            },
            dataType : "json",                     // тип загружаемых данных
            complete: function () { // вешаем свой обработчик на функцию success
                reloadIntervals();
            }
        });

    });


    $(document).on('click', '.update', function () {
        var row = $(this.parentElement.parentElement);
        $.ajax({
            url: api_address + '/updateInterval',             // указываем URL и
            method: 'POST',
            data: {
                current_id: row.find('td:eq(0)>input')[0].value,
                current_date_start: row.find('td:eq(1)>input')[0].value,
                current_date_end: row.find('td:eq(2)>input')[0].value,
                current_price: row.find('td:eq(3)>input')[0].value,
                new_id: row.find('td:eq(0)>input')[1].value,
                new_date_start: row.find('td:eq(1)>input')[1].value,
                new_date_end: row.find('td:eq(2)>input')[1].value,
                new_price: row.find('td:eq(3)>input')[1].value,
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
            url: api_address + '/addInterval',             // указываем URL и
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
});