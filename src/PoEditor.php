<?php
/**
 * Created by PhpStorm.
 * User: Nenad
 * Date: 4/5/2019
 * Time: 1:31 PM
 */

namespace NMilosavljevic\PoEditor;

use Gettext\Merge;
use Gettext\Translation;
use \NMilosavljevic\PoEditor\Translations\Translations;
use \NMilosavljevic\PoEditor\Exceptions\FileException;
use \NMilosavljevic\PoEditor\Exceptions\PoEditorException;


/**
 * Class PoEditor
 * @package NMilosavljevic\PoEditor
 * @property Translations $translations
 */
class PoEditor
{
    const PHPFile = 1;
    const TwigFile = 2;
    const RawTwigFile = 3;

    /**
     * PoEditor constructor.
     * @param Translations|null $translations
     */
    public function __construct($translations = null)
    {
        $this->translations = $translations === null ? new Translations() : $translations;
    }


    //region public functions

    /**
     * Reads existing translations from PO file
     * If translation keyword exists in $translations, translation is set from PO file.
     * Returns read translations;
     * @param $filePath
     * @return Translations | null
     * @throws FileException
     * @throws PoEditorException
     */
    public function readFromPOFile($filePath)
    {
        $poTranslations = null;
        if (is_file($filePath)) {
            $fileInfo = pathinfo($filePath);
            if ($fileInfo && isset($fileInfo['extension']) && $fileInfo['extension'] === 'po') {
                $poTranslations = Translations::fromPoFile($filePath);
            } else {
                throw new PoEditorException(sprintf('Provided path %s does not specify .PO file', $filePath));
            }
        } else {
            throw new FileException(sprintf('Provided path %s does not specify file', $filePath));
        }

        $this->mergeFromPOFile($poTranslations);
        return $poTranslations;
    }

    /**
     * @param $options
     * @return Translations
     * @throws PoEditorException
     */
    public function fromTwigFile($options)
    {
        if (isset($options['parser']) && $options['parser'] === 'raw') {
            return $this->getFromFile($options, self::RawTwigFile);
        }
        return $this->getFromFile($options, self::TwigFile);
    }

    /**
     * @param $options
     * @return Translations
     * @throws PoEditorException
     */
    public function fromPHPCodeFile($options)
    {
        return $this->getFromFile($options, self::PHPFile);
    }


    /**
     * @param null $poFilePath
     * @param null $translations
     * @return false|string
     * @throws FileException
     * @throws PoEditorException
     */
    public function getEditorHTML($poFilePath = null, $translations = null)
    {
        if ($translations === null) {
            $translations = $this->translations;
        }

        $existingTranslations = null;
        if ($poFilePath !== null) {
            if (is_file($poFilePath)) {
                $info = pathinfo($poFilePath);
                if (!isset($info['extension']) || $info['extension'] !== 'po') {
                    throw new PoEditorException(sprintf('Provided file %s is not a PO file', $poFilePath));
                }
                $existingTranslations = Translations::fromPoFile($poFilePath);
            } else {
                throw new FileException(sprintf('Provided path %s is not a file path', $poFilePath));
            }

            foreach ($translations as $translation) {
                $existingTranslation = $existingTranslations->find('', $translation->getOriginal());
                if ($existingTranslation) {
                    $translation->setTranslation($existingTranslation->getTranslation());
                }
            }
        }
        return $this->renderHTML();
    }

    /**
     * @param $directory
     * @param Translations $translations
     * @param null $language
     * @param string $fileName
     * @return bool
     * @throws FileException
     * @throws PoEditorException
     */
    public function saveTranslationFiles($directory,$translations = null, $language = null, $fileName = '$translations')
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory,0777,true) && !is_dir($directory)) {
                throw new FileException(sprintf('Directory "%s" does not exist', $directory));
            }
        }
        if ($translations === null) {
            $translations = $translations;
        }
        if ($language !== null) {
            $translations->setLanguage($language);
        }

        $success = $translations->toPoFile(sprintf('%s/%s.po', $directory, $fileName));
        if (!$success) {
            throw new PoEditorException(sprintf('.PO file was not saved at %s/%s', $directory, $fileName));
        }
        $success = $translations->toMoFile(sprintf('%s/%s.mo', $directory, $fileName)) && $success;
        if (!$success) {
            throw new PoEditorException(sprintf('.MO file was not saved at %s/%s', $directory, $fileName));
        }
        return $success;
    }

    /**
     * @param $directory
     * @param $translationDTOs
     * @param string $fileName
     * @param null $language
     * @return bool
     * @throws FileException
     * @throws PoEditorException
     */
    public function saveFromEditor($directory, $translationDTOs, $fileName='translations', $language = null)
    {
        $translations = new Translations();
        if ($language !== null) {
            $translations->setLanguage($language);
        }
        foreach ($translationDTOs as $translationDTO) {
            $translation = $translations->insert($translationDTO['context'],$translationDTO['original']);
            $translation->setTranslation($translationDTO['translation']);
        }
        return $this->saveTranslationFiles($directory,$translations,$language,$fileName);
    }

    public function mergeTranslations($translations)
    {
        $this->translations->mergeWith($translations);
    }
    //endregion

    //region private functions

    /**
     * @param Translations $translations
     */
    private function mergeFromPOFile($translations)
    {
        foreach ($this->translations as $translation) {
            $tempTrans = $translations->find($translation->getContext(), $translation->getOriginal());
            if ($tempTrans) {
                $translation->setTranslation($tempTrans->getTranslation());
            }
        }
    }

    /**
     * @param $options
     * @param $fileType
     * @return Translations
     * @throws PoEditorException
     */
    private function getFromFile($options, $fileType)
    {
        $poOptions = $this->setOptions($options);
        $callFunc = null;
        $extension = null;
        //ToDo::cases for all file extensions
        switch ($fileType) {
            case self::PHPFile:
                $callFunc = 'fromPhpCodeFile';
                $extension = 'php';
                break;
            case self::TwigFile:
                $callFunc = 'fromTwigFile';
                $extension = 'twig';
                break;
            case self::RawTwigFile:
                $callFunc = 'fromRawTwigFile';
                $extension = 'twig';
                break;
            default:
                break;
        }


        if ($callFunc === null) {
            Throw new PoEditorException(sprintf('Generate from unknown file [%s] type requested', $fileType));
        }
        $tempTranslations = new Translations();
        if (isset($options['file'])) {
            if ($options ['file']) {
                $tempPo = Translations::$callFunc($options['file']);
                $tempTranslations->mergeWith($tempPo);
            }
        }
        if (isset($options['directories'])) {
            if ($options['directories'] && is_array($options['directories'])) {
                foreach ($options['directories'] as $directory) {
                    $files = $this->getFiles($directory, $extension, $files);
                    foreach ($files as $file) {
                        $tempPo = Translations::$callFunc($file, $poOptions);
                        $tempTranslations->mergeWith($tempPo);
                    }
                }
            }
        }
        $this->mergeTranslations($tempTranslations);
        return $tempTranslations;
    }


    //sets options which are used to extract transltaions from files
    private function setOptions($tempOptions)
    {
        $options = [];
        if (isset($tempOptions['functions'])) {
            $options['functions'] = $tempOptions['functions'];
        }
        if (isset($tempOptions['twig'])) {
            $options['twig'] = $tempOptions['twig'];
        }
        return $options;
    }

    //recursively get files from directories
    private function getFiles($dir, $extension, &$results = array())
    {
        $files = scandir($dir, SCANDIR_SORT_NONE);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value !== '.' && $value !== '..') {
                $this->getFiles($path, $extension, $results);
                $results[] = $path;
            }
        }
        //only php files
        $filesArray = [];
        foreach ($results as $key => $fileName) {
            $info = pathinfo($fileName);
            if (isset($info['extension'])) {
                if ($info['extension'] === $extension) {
                    $filesArray[] = $fileName;
                }
            } else {
                $c = 5;
            }
        }

        return $filesArray;
    }

    private function renderHTML($translations = null)
    {
        if ($translations === null) {
            $translations = $this->translations;
        }

        extract(['translations' => $translations], EXTR_OVERWRITE);
        ob_start();
        include 'View/editor.php';
        return ob_get_clean();
    }
    //endregion
}