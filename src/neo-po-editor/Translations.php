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
    public function generateTranslationsFromPhpCodeFile($options)
    {
        $translations = new Translations();
        if ($options ['file']) {
            $tempPo = Translations::fromPhpCodeFile($options ['file'], ['functions' => ['translate' => 'gettext']]);
            $translations->mergeWith($tempPo);
        }
        if ($options['directories'] && is_array($options['directories'])) {
            foreach ($options['directories'] as $directory) {
                $files = $this->getPhpFiles($directory, $files);
                foreach ($files as $file) {
                    $tempPo = Translations::fromPhpCodeFile($file, ['functions' => ['translate' => 'gettext']]);
                    $translations->mergeWith($tempPo);
                }
            }
        }
        //ToDo: other options;
        return $translations;
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

    public function SaveTranslationsToPoMoFile($existingTranslations, $translationsDirectory, $domain,  $locale)
    {
        $localeDir = $locale . '_locale';
        $localeDir = sprintf('%s/%s', $translationsDirectory, $localeDir);
        $messagesDir = sprintf('%s/%s',$domain, $localeDir);
        $filesDir = sprintf('%s/', $messagesDir);
        if (!is_dir($localeDir)) {
            if (!mkdir($localeDir) && !is_dir($localeDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $localeDir));
            }
        }
        if (!is_dir($messagesDir)) {
            if (!mkdir($messagesDir) && !is_dir($messagesDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $messagesDir));
            }
        }


        $existingTranslations->toPoFile(sprintf('%s/translations.po', $filesDir));
        $existingTranslations->toMoFile(sprintf('%s/translations.mo', $filesDir));
    }
}