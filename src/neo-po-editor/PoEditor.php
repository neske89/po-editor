<?php
/**
 * Created by PhpStorm.
 * User: Nenad
 * Date: 4/5/2019
 * Time: 1:31 PM
 */

namespace NMilosavljevic\PoEditor;

use Gettext\Translations;

class PoEditor
{
    /**
     * @param []$options
     * @return Translations
     */

    const PHPFile = 1;
    const TwigFile = 2;

    public function fromTwigFile($options) {
        return $this->getFromFile($options,self::TwigFile);
    }

    public function fromPHPCodeFile($options)
    {
        return $this->getFromFile($options,self::PHPFile);
    }



    private function getFromFile ($options,$fileType)
    {
        $callFunc = null;
        switch ($fileType) {
            case self::PHPFile:
                $callFunc = 'fromPhpCodeFile';
                break;
            case self::TwigFile:
                $callFunc = 'fromTwigFile';
                break;
            default:
        }

        if ($callFunc === null) {
            return null;
        }
        $translations = new Translations();
        if ($options ['file']) {
            $tempPo = forward_static_call(array(Translations::class,$callFunc,$options));
            $translations->mergeWith($tempPo);
        }
        if ($options['directories'] && is_array($options['directories'])) {
            foreach ($options['directories'] as $directory) {
                $files = $this->getPhpFiles($directory, $files);
                foreach ($files as $file) {
                    $tempPo = forward_static_call(array(Translations::class,$callFunc,$options));
                    $translations->mergeWith($tempPo);
                }
            }
        }
        //ToDo: other options;
        return $translations;
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
    public function SaveTranslationsToPoMoFile($translations, $directoryPath, $locale, $domain = 'LC_MESSAGES' )
    {
        $localeDir = $locale . '_locale';
        $localeDir = sprintf('%s/%s', $directoryPath, $localeDir);
        $domainDirectory = sprintf('%s/%s',$domain, $localeDir);
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

    private function getPhpFiles($dir, &$results = array())
    {
        $files = scandir($dir, SCANDIR_SORT_NONE);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value !== '.' && $value !== '..') {
                $this->getPhpFiles($path, $results);
                $results[] = $path;
            }
        }
        //only php files
        $filesArray = [];
        foreach ($results as $key => $fileName) {
            $info = pathinfo($fileName);
            if ($info['extension'] === 'php') {
                $filesArray[] = $fileName;
            }
        }

        return $filesArray;
    }

}