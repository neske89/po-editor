$(document).ready(function () {
    $('td').on('click', function () {
        $(this).parent().find('textarea').focus();
    });

    $('#save-changes').on('click',function(){
        $('#translations-form').submit();
    });
});