$(document).ready(function () {

    $('.content').css('padding-top',$('.sticky').height());

    $('td').on('click', function () {
        markActiveRow(this);
    });
    $('body').on('focus','textarea', function () {
        markActiveRow(this);
    });

    $('body').on('change', 'textarea.translation-translation', function () {
        let translation = $(this).closest('tr').data('translation');
        translation.translation = $(this).val();
        $(this).closest('tr').data('translation', translation);
    });

    $('#save-changes').on('click', function () {
        let url = window.location.href;
        let translations = [];
        $('table tbody tr').each(function (index, value) {
            translations.push($(this).data('translation'));
        });

        $.ajax({
            url: url,
            method: 'POST',
            data:
                {
                    translations: translations
                },
            success:function() {
                window.location.reload();
            }
        });

    });

    function markActiveRow(element) {
        $('tr.active').removeClass('active');
        $(element).closest('tr').addClass('active');
    }

});