<?php
declare(strict_types=1);

namespace vDesk\IO\Stream;

/**
 * Enumeration of Stream lock types.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Lock {

    /**
     * Sets a shared lock, so any other processes can read or write too.
     */
    public const Shared = \LOCK_SH;

    /**
     * Sets an exclusive lock for the process which opened the stream.
     */
    public const Exclusive = \LOCK_EX;

    /**
     * Releases a set lock.
     */
    public const Free = \LOCK_UN;

    /**
     * Sets a non-blocking lock.
     * The NonBlocking option can be set as a bit-mask.
     * <code>
     * $Stream->Lock(IO\Stream\Lock::Shared | IO\Stream\Lock::NonBlocking);
     * </code>
     */
    public const NonBlocking = \LOCK_NB;

}
