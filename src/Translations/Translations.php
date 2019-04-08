<?php
/**
 * Created by PhpStorm.
 * User: Nenad
 * Date: 4/8/2019
 * Time: 2:57 PM
 */
namespace NMilosavljevic\PoEditor\Translations;

use Gettext\Extractors\Twig;
use NMilosavljevic\PoEditor\Extractors\RawTwigExtractor;

class Translations extends \Gettext\Translations
{

    public static function fromRawTwigFile($file, $poOptions) {
        $translations = new Translations();
        return RawTwigExtractor::fromFile($file,$translations,$poOptions);
    }
    
}