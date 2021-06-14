<?php
declare(strict_types=1);

namespace vDesk\Crash\Test;

use vDesk\Crash\Attribute;

#[Attribute]
class Test extends Attribute {

    public function __construct(public array $Arguments = []) {

    }
}