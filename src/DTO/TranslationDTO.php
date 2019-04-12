<?php
/**
 * Created by PhpStorm.
 * User: Nenad
 * Date: 4/8/2019
 * Time: 2:57 PM
 */
namespace NMilosavljevic\PoEditor\DTO;

use Gettext\Translation;

/**
 * @property string original
 * @property string context
 * @property string translation
 * @property array $references
 */
class TranslationDTO
{


    /**
     * TranslationDTO constructor.
     * @param Translation $translation
     */
    public function __construct($translation)
    {
        $this->translation = $translation->getTranslation();
        $this->original = $translation->getOriginal();
        $this->references = $translation->getReferences();
        $this->context = $translation->getContext();
    }
}