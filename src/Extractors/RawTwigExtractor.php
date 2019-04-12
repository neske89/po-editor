<?php
/**
 * Created by PhpStorm.
 * User: Nenad
 * Date: 4/8/2019
 * Time: 2:57 PM
 */

namespace NMilosavljevic\PoEditor\Extractors;

use Gettext\Extractors\Extractor;
use Gettext\Translations;

class RawTwigExtractor extends Extractor
{
    /**
     * @param $file
     * @param Translations $translations
     * @param array $options
     * @return Translations
     * @throws \Exception
     */
    public static function fromFile($file, Translations $translations, array $options = [])
    {
        $string = self::readFile($file);

        $matches = [];
        if (isset($options['functions']) && is_array($options['functions'])) {
            foreach ($options['functions'] as $rawFunction => $getTextFunction) {
                preg_match_all('#' . $rawFunction . '\((.*?)\)#', $string, $matches, PREG_OFFSET_CAPTURE);
            }
        }

        $lineEndingChar = "\n";
        if (isset($options['lineEnding'])) {
            $lineEndingChar = $options['lineEnding'];
        }

        if ($matches[1]) {
            foreach ($matches[1] as $index => $translationKey) {
                $line = substr_count(substr($string, 0, $matches[1][$index][1]), $lineEndingChar) + 1;

                $translationKey = $translationKey[0];
                //remove " or ' which are used to wrap up function argument
                if ($translationKey[0] === '"' && $translationKey[strlen($translationKey) - 1] === '"') {
                    $translationKey = trim($translationKey,'"');
                } else if ($translationKey[0] === "'" && $translationKey[strlen($translationKey) - 1] === "'") {
                    $translationKey = trim($translationKey,"'");
                }

                $translation = $translations->insert('', $translationKey, '');
                $translation->addReference($file, $line);
            }
        }
        return $translations;
    }

    abstract public static function fromString($string, Translations $translations, array $options = []);



}