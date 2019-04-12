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
		 //editor class has internal reference of translations
		 // $editor->translations
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

    `$twigTranslations = $editor->fromTwigFile([
     'parser' => 'raw', //required
     'functions' => ['__' => 'gettext'], //name of the function in twig file is __
      //path to the directory which contain multiple twigfiles
     'directories' => [sprintf('%s/Template/Default', $portalPath)]]);`

#### Merge translations
     $twigTranslations->mergeWith($phpTranslations);
#### Read from PO file
    $editor->readFromPOFile('App/Locale/en/translations.po');
#### SaveTranslations
    $editor->saveTranslationFiles('App/Locale/en/',$twigTranslations,'en','messages');
    //or
    $editor->SaveTranslationsToPoMoFile('App/Locale/en/');
    //in this case, $editor->translations are used
#### Get Editor HTML
      `$editor = new \NMilosavljevic\PoEditor\PoEditor();
       $shortcode = 'it';
      $portalPath = str_replace('Dashboard', 'Portal', PROJECT_DIR);
      $poFilePath = sprintf('%s/Locale/%s/LC_MESSAGES/messages.po',$portalPath,$shortcode);
       
       try{
            $twigTranslations = $editor->fromTwigFile([
                 'parser' => 'raw',
                 'functions' => ['__' => 'gettext'],
                 'directories' => [sprintf('%s/Template/Default', $portalPath)]]);
       
            $phpTranslations = $editor->fromPHPCodeFile([
                'functions' => ['__' => 'gettext'],
                 'directories' => [sprintf('%s/Helper', $portalPath)]]);
            
            //variables $twigTranslations and $phpTranslations are not used here, they are just declared
            //to show that function could return exported translations
            
            //in this case, translations exported from twig and php files are merged into $editor->translations
            //translations read from PO file will also be merged into $editor->translations
            
            $editor->readFromPOFile($poFilePath);
            $html = $editor->getEditorHTML();`
            
            //render html in framework of your choice       
####  Save translations Using Editor
Submiting the translations from the editor (by clicking save button) will send post request
to the same url with parameters translations and optional parameter language.

Those parameters should be passed to saveFromEditor function

        `$editor = new \NMilosavljevic\PoEditor\PoEditor();
        $shortcode = 'it';
        $portalPath = str_replace('Dashboard', 'Portal', PROJECT_DIR);
        $directory = sprintf('%s/Locale/%s/LC_MESSAGES/',$portalPath,$shortcode);
        $success = $editor->saveFromEditor($directory,$translations,'messages',$shortcode);`
        //handle sending OK response here so that page could refresh.


