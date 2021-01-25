<?php
declare(strict_types=1);

namespace vDesk\Locale;

use vDesk\Struct\Collections\Dictionary;

/**
 * Class TranslationDictionary represents ...
 *
 * @package vDesk\Locale
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class TranslationDictionary extends Dictionary {
    
    /**
     * @inheritdoc
     */
    public function offsetGet($Index): string {
        return $this->offsetExists($Index) ? parent::offsetGet($Index) : LocaleDictionary::UndefinedTranslation;
    }
    
}