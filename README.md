# PoEditor
PHP library which provides PHP web interface to edit or create po files.

## Credits
Library heavily relies on [https://github.com/oscarotero/Gettext](https://github.com/oscarotero/Gettext).

## Instalation
Using composer

## Functionalities
#### Generate translation keywords
Translations keywords could be generated from **php code files** or **twig files**, Support for more formats will be available in the future. 
PHP code file and twig file translations could be mergded to form single translation file.
After each generation, unused keywords will be removed and new ones will be added to the list of the translations.
#### Reading .PO file
Existing translations could be read from PO file.
#### Provides premade user interface for translating
Users could use already pre-made translations form, or you could build your own UI for translating.
#### Export to .PO & ,MO files
With the location of files provided created or edited translations could be exported to .PO and .MO file.
 
## Usage examples

### Instantiating
		 $editor = new \NMilosavljevic\PoEditor\PoEditor();
		 
### Generating translations from PHP code file
		$phpTranslations = $editor->fromPHPCodeFile([
		'functions' => ['__' => 'gettext'],
		'directories' => ['App/Helpers/']]);
### Generating translations from TWIG  file
			$twigTranslations = $editor->fromTwigFile([
		'functions' => ['__' => 'gettext'],
		'directories' => [sprintf('%s/Helper', 'App/Template/)]]);
#### If TWIG file uses custom function to translate
Twig:

		`<h3><strong>{{ __("who_are_we") }}</strong></h3>`

PHP:

    $twigTranslations = $editor->fromTwigFile([
     'parser' => 'raw',$
     'functions' => ['__' => 'gettext'],
     'directories' => [sprintf('%s/Template/Default', $portalPath)]]);

#### Merge translations

     $twigTranslations->mergeWith($phpTranslations);

#### Read from PO file

    $editor->getTranslationsFromPOFile('App/Locale/en/translations.po');
#### SaveTranslations

    $editor->SaveTranslationsToPoMoFile($twigTranslations,'App/Locale/en/');

#### Get Editor HTML

####  Save translations Using Editor
`


