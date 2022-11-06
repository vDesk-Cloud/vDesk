<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Trait for streams that support read-, write- and/or seek-operations.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
trait Stream {

    /**
     * The underlying pointer of the Stream.
     *
     * @var null|resource
     */
    protected $Pointer;

    /**
     * Tells whether the current Stream has reached its end. EndOfStream is a convenience method that is equivalent to the value of the
     * EndOfStream property of the current instance.
     *
     * @return bool True if the Stream has reached its end; otherwise, false.
     */
    public function EndOfStream(): bool {
        return \feof($this->Pointer);
    }

    /**
     * Frees any resources occupied by the Stream.
     */
    public function __destruct() {
        if(\is_resource($this->Pointer)) {
            \fclose($this->Pointer);
        }
    }

}