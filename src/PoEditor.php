<?php
/**
 * Created by PhpStorm.
 * User: Nenad
 * Date: 4/5/2019
 * Time: 1:31 PM
 */

namespace NMilosavljevic\PoEditor;

use \NMilosavljevic\PoEditor\Translations\Translations;

class PoEditor
{
    /**
     * @param []$options
     * @return Translations
     */

    const PHPFile = 1;
    const TwigFile = 2;
    const RawTwigFile = 3;

    public function getEditorHTML($poFilePath, $generatedTranslations)
    {
        $existingTranslations = null;
        if (is_file($poFilePath)) {
            $info = pathinfo($poFilePath);
            if (!isset($info['extension']) || $info['extension'] !== 'po') {
                throw new FileException(sprintf('Provided file %s is not a PO file', $poFilePath));
            }
            $existingTranslations = Translations::fromPoFile($poFilePath);
        }

        if ($existingTranslations) {
            foreach ($generatedTranslations as $translation) {
                $existingTranslation = $existingTranslations->find('', $translation->getOriginal());
                if ($existingTranslation) {
                    $translation->setTranslation($existingTranslation->getTranslation());
                }
            }
        }

        return $this->renderHTML($generatedTranslations);
    }
    
    /**
     * @param string $filePath
     * @return Translations|null
     */
    public function getTranslationsFromPOFile($filePath)
    {
        if (is_file($filePath)) {
            return Translations::fromPoFile($filePath);
        }
        return null;
    }
    /**
     * @param Translations $translations
     * @param String $directoryPath
     * @param String $domain
     * @param String $locale
     * @throws FileException
     */
    public function saveTranslationsToPoMoFile($translations, $directoryPath, $locale, $domain = 'LC_MESSAGES')
    {
        $localeDir = $locale . '_locale';
        $localeDir = sprintf('%s/%s', $directoryPath, $localeDir);
        $domainDirectory = sprintf('%s/%s', $domain, $localeDir);
        $filesDir = sprintf('%s/', $domainDirectory);
        if (!is_dir($localeDir)) {
            if (!mkdir($localeDir) && !is_dir($localeDir)) {
                throw new FileException(sprintf('Directory "%s" was not created', $localeDir));
            }
        }
        if (!is_dir($domainDirectory)) {
            if (!mkdir($domainDirectory) && !is_dir($domainDirectory)) {
                throw new FileException(sprintf('Directory "%s" was not created', $domainDirectory));
            }
        }

        $translations->toPoFile(sprintf('%s/translations.po', $filesDir));
        $translations->toMoFile(sprintf('%s/translations.mo', $filesDir));
    }
    public function fromTwigFile($options)
    {
        if (isset($options['parser']) && $options['parser'] === 'raw') {
            return $this->getFromFile($options, self::RawTwigFile);
        }
        return $this->getFromFile($options, self::TwigFile);
    }
    public function fromPHPCodeFile($options)
    {
        return $this->getFromFile($options, self::PHPFile);
    }
    private function renderHTML($translations) {
        extract(['translations'=>$translations],EXTR_OVERWRITE);
        ob_start();
        include('View/editor.php');
        return ob_get_clean();
    }
    private function getFromFile($options, $fileType)
    {
        $poOoptions = $this->setOptions($options);

        $callFunc = null;
        $extension = null;
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
            return null;
        }
        $translations = new Translations();

        if (isset($options['file'])) {
            if ($options ['file']) {
                $tempPo = Translations::$callFunc($options['file']);
                $translations->mergeWith($tempPo);
            }
        }
        if (isset($options['directories'])) {
            if ($options['directories'] && is_array($options['directories'])) {
                foreach ($options['directories'] as $directory) {
                    $files = $this->getFiles($directory, $extension, $files);
                    foreach ($files as $file) {
                        $tempPo = Translations::$callFunc($file, $poOoptions);
                        $translations->mergeWith($tempPo);
                    }
                }
            }
        }
        //ToDo: other options;
        return $translations;
    }
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
}