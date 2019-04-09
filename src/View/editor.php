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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Translations Editor</a>
    <button id="save-changes" class="btn btn-success">Save</button>
</nav>

<div class="">
    <?php /** @var \Gettext\Translation $translation */ ?>
    <form id="translations-form">
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th class="text-center">Orignal</th>
            <th class="text-center">Translation</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($translations as $translation): ?>
            <tr>
                <td><?php echo $translation->getOriginal() ?></td>
                <td class="translation">
                <textarea name="translations" type="text" style="width:100%;height:100%"
                          placeholder="Enter translation"><?php if (!empty($translation->getTranslation())) {
                        echo $translation->getTranslation();
                    } ?></textarea>
                    <input type="hidden" name="translationKeys" value="<?php echo htmlspecialchars($translation->getOriginal())?>"
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    </form>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<script><?php include('editor.js') ?></script>

</body>
</html>