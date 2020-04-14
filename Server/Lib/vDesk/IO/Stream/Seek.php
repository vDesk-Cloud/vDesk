<?php

namespace vDesk\IO\Stream;

/**
 * Enumeration of stream seek types.
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
abstract class Seek {

    /**
     * Sets the position to the specified offset.
     */
    public const Offset = SEEK_SET;

    /**
     * Sets the position to the specified offset after the current position.
     */
    public const Current = SEEK_CUR;

    /**
     * Sets the position to the specified offset after the end-of-file.
     */
    public const End = SEEK_END;

}