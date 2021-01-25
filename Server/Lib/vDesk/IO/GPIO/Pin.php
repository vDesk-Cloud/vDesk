<?php
declare(strict_types=1);

namespace vDesk\IO\GPIO;


class Pin {
    public function __construct(int $Pin, int $Direction, $Value = null) {
        `echo "{$Pin}" > /sys/class/gpio/export`;
    }
}