<?php
declare(strict_types=1);

namespace vDesk\Crash\Test\Case;

use vDesk\Crash\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Penetrate extends Attribute {

    public function __construct(public array $Arguments = [], public ?int $Amount = null, public ?int $Interval = null) {
        parent::__construct($Arguments);
    }

}