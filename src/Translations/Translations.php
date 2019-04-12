<?php
/**
 * Created by PhpStorm.
 * User: Nenad
 * Date: 4/8/2019
 * Time: 2:57 PM
 */
namespace NMilosavljevic\PoEditor\Translations;

use NMilosavljevic\PoEditor\Extractors\RawTwigExtractor;

class Translations extends \Gettext\Translations
{

    /**
     * @param $file
     * @param $poOptions
     * @return \Gettext\Translations
     * @throws \Exception
     */
    public static function fromRawTwigFile($file, $poOptions) {
        $translations = new self();
        return RawTwigExtractor::fromFile($file,$translations,$poOptions);
    }
    
}