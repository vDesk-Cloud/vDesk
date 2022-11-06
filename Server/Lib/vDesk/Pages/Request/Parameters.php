<?php
declare(strict_types=1);

namespace vDesk\Pages\Request;

use vDesk\IO\Input;
use vDesk\Struct\Collections\Dictionary;

/**
 * Class that represents a CGI and message body parameter dictionary.
 *
 * @package vDesk\Pages\Request
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Parameters extends Dictionary {

    /** @inheritDoc */
    public function offsetGet($Key) {
        if($this->offsetExists($Key)) {
            return parent::offsetGet($Key);
        }
        $this->Add($Key, Input::ParseCommand($Key) ?? Input::ParseParameter($Key));
        return parent::offsetGet($Key);
    }
}