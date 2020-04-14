<?php
declare(strict_types=1);

namespace vDesk\Locale;

use vDesk\Struct\Collections\Dictionary;

/**
 * Class DomainDictionary represents ...
 *
 * @package vDesk\Locale
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class DomainDictionary extends Dictionary {

    /**
     * @inheritdoc
     */
    public function offsetGet($Key) {
        if($this->offsetExists($Key)) {
            return parent::offsetGet($Key);
        }
        $this->Add($Key, new EmptyDictionary(LocaleDictionary::UndefinedTranslation));
        return parent::offsetGet($Key);
    }

}