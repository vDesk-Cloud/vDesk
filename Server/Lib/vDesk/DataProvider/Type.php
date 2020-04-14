<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Enumeration of bit set flags of available database field types.
 *
 * @package vDesk\Connection\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
abstract class Type {
    public const TinyInt    = 0b00000000000000000000001;
    public const SmallInt   = 0b00000000000000000000010;
    public const Int        = 0b00000000000000000000100;
    public const BigInt     = 0b00000000000000000001000;
    public const Boolean    = 0b00000000000000000010000;
    public const Decimal    = 0b00000000000000000100000;
    public const Float      = 0b00000000000000001000000;
    public const Double     = 0b00000000000000010000000;
    public const Char       = 0b00000000000000100000000;
    public const VarChar    = 0b00000000000001000000000;
    public const TinyText   = 0b00000000000010000000000;
    public const Text       = 0b00000000000100000000000;
    public const MediumText = 0b00000000001000000000000;
    public const LongText   = 0b00000000010000000000000;
    public const Timestamp  = 0b00000000100000000000000;
    public const Date       = 0b00000001000000000000000;
    public const Time       = 0b00000010000000000000000;
    public const DateTime   = 0b00000100000000000000000;
    public const TinyBlob   = 0b00001000000000000000000;
    public const Blob       = 0b00010000000000000000000;
    public const MediumBlob = 0b00100000000000000000000;
    public const LongBlob   = 0b01000000000000000000000;
    public const Unsigned   = 0b10000000000000000000000;
}