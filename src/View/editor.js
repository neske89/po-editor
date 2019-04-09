$(document).ready(function () {
    $('td').on('click', function () {
        $(this).parent().find('textarea').focus();
    });
});