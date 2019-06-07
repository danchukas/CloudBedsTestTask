const api_address = 'http://localhost:8069';

function reloadIntervals() {
    $.ajax({
        url: api_address + '/getFullPriceList',             // указываем URL и
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
            url: api_address + '/deleteInterval',             // указываем URL и
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