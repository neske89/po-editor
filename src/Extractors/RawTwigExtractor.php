<?php
/**
 * Created by PhpStorm.
 * User: Nenad
 * Date: 4/8/2019
 * Time: 2:57 PM
 */

namespace NMilosavljevic\PoEditor\Extractors;

use Gettext\Extractors\Extractor;
use Gettext\Extractors\Twig;
use Gettext\Translations;

class RawTwigExtractor extends Extractor
{

    public static function fromFile($file, Translations $translations, array $options = [])
    {
        $string = self::readFile($file);

        $matches = [];
        if (isset($options['functions']) && is_array($options['functions'])) {
            foreach ($options['functions'] as $rawFunction => $getTextFunction) {
                preg_match_all('#' . $rawFunction . '\((.*?)\)#', $string, $matches,PREG_OFFSET_CAPTURE);
            }
        }

        $lineEndingChar = "\n";
        if (isset($options['lineEnding'])) {
            $lineEndingChar = $options['lineEnding'];
        }

        if ($matches[1]) {
            foreach ($matches[1] as $index => $translationKey) {
                $line = substr_count(substr($string, 0, $matches[1][$index][1]), $lineEndingChar) + 1;
                $line1 = substr_count(substr($string, 0, $matches[1][$index][1]),"\r") + 1;

                $translation = $translations->insert('', $translationKey[0], '');
                $translation->addReference($file, $line);
            }
        }
        return $translations;
    }

    public static function fromString($string, Translations $translations, array $options = [])
    {
        return false;
        //ToDo: declare as abstract
    }


}