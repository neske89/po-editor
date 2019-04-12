<?php
/** @var NMilosavljevic\PoEditor\Translations\Translations $translations */
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style><?php include('editor.css') ?></style>
    <title>Translations Editor</title>
</head>
<body>
<div class="sticky">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark d-block">
        <a class="navbar-brand" href="#">Translations Editor</a>
        <button id="save-changes" class="btn btn-success float-right text-uppercase"><strong>Save</strong></button>
    </nav>
    <div class="row no-gutters header-row">
        <div class="col-6 text-center"><h5>Original</h5></div>
        <div class="col-6 text-center"><h5>Translation</h5></div>
    </div>
</div>

<div class="content">
    <?php /** @var \Gettext\Translation $translation */ ?>
    <table class="table table-bordered table-hover">
        <tbody>
        <tr id="translation-template">
            <td class="translation-original"></td>
            <td class="translation">
                <textarea class="translation-translation" name="translations" type="text" style="width:100%;height:100%"
                          placeholder="Enter translation"></textarea>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script
        src="https://code.jquery.com/jquery-3.4.0.min.js"
        integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<script type="text/javascript">
    translations = [];
    <?php foreach ($translations as $translation): ?>
    var json = "<?php echo base64_encode(json_encode(new \NMilosavljevic\PoEditor\DTO\TranslationDTO($translation))) ?>";
    var translation = JSON.parse(atob(json));
    translations.push(translation);
    <?php endforeach;?>
    let $template = $('#translation-template');
    for (index in translations) {
        let translation = translations[index];
        let $clone = $template.clone();
        $clone.attr('id', 'translation-' + index);
        $clone.find('.translation-original').text(translation.original);
        $clone.find('.translation-translation').val(translation.translation);
        $template.before($clone);
        $clone.data('translation', translation);
    }
    $template.remove();
</script>
<script><?php include('editor.js') ?></script>

</body>
</html>