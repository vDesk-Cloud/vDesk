<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;

/**
 * Utility class for table related MsSQL Expressions providing functionality for creating fields and indexes.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Table {

    /**
     * Enumeration of supported MsSQL type mappings.
     */
    public const Collations = [
        Collation::ASCII                     => "Latin1_General_100_CI_AI",
        Collation::ASCII | Collation::Binary => "Latin1_General_100_BIN2",
        Collation::UTF8                      => "Latin1_General_100_CI_AI_SC_UTF8",
        Collation::UTF8 | Collation::Binary  => "Latin1_General_100_BIN2_UTF8",
        Collation::UTF16                     => "Latin1_General_100_CI_AI_SC_UTF8",
        Collation::UTF16 | Collation::Binary => "Latin1_General_100_BIN2_UTF8",
        Collation::UTF32                     => "Latin1_General_100_CI_AI_SC_UTF8",
        Collation::UTF32 | Collation::Binary => "Latin1_General_100_BIN2_UTF8"
    ];

    /**
     * Enumeration of MsSQL specified type mappings.
     */
    public const Types = [
        Type::TinyInt    => "TINYINT",
        Type::SmallInt   => "SMALLINT",
        Type::Int        => "INT",
        Type::BigInt     => "BIGINT",
        Type::Boolean    => "TINYINT",
        Type::Decimal    => "DECIMAL",
        Type::Float      => "REAL",
        Type::Double     => "DOUBLE PRECISION",
        Type::Char       => "CHAR",
        Type::VarChar    => "VARCHAR",
        Type::TinyText   => "VARCHAR(255)",
        Type::Text       => "TEXT",
        Type::MediumText => "TEXT",
        Type::LongText   => "TEXT",
        Type::Timestamp  => "TIMESTAMP",
        Type::Date       => "DATE",
        Type::Time       => "TIME",
        Type::DateTime   => "DATETIME",
        Type::TinyBlob   => "VARBINARY(255)",
        Type::Blob       => "VARBINARY(65535)",
        Type::MediumBlob => "VARBINARY(16777215)",
        Type::LongBlob   => "VARBINARY(MAX)"
    ];

    /**
     * Creates a MsSQL conform table field.
     *
     * @param string      $Name          The name of the table field.
     * @param int         $Type          The type of the table field.
     * @param int|null    $Size          The size of the table field.
     * @param null|int    $Collation     The collation of the table field.
     * @param bool        $Nullable      Flag indicating whether the table field is nullable.
     * @param mixed       $Default       The default value of the table field.
     * @param bool        $AutoIncrement The size of the table field.
     * @param string|null $OnUpdate      The size of the table field.
     *
     * @return string A MsSQL conform table field.
     */
    public static function Field(
        string  $Name,
        int     $Type,
        bool    $Nullable = false,
        bool    $AutoIncrement = false,
        mixed   $Default = "",
        ?int    $Collation = null,
        ?int    $Size = null,
        ?string $OnUpdate = null
    ): string {

        $Field = [DataProvider::EscapeField($Name)];

        //Create type and collation.
        if($Collation !== null) {
            if($Collation & ~Collation::ASCII & ~Collation::Binary) {
                $Field[] = match ($Type & ~Type::Unsigned) {
                    Type::Char,
                    Type::VarChar => "N" . static::Types[$Type & ~Type::Unsigned] . ($Size !== null ? "({$Size})" : ""),
                    Type::TinyText,
                    Type::Text,
                    Type::MediumText,
                    Type::LongText => "N" . static::Types[$Type & ~Type::Unsigned],
                    default => ""
                };
            } else {
                $Field[] = static::Types[$Type & ~Type::Unsigned]
                           . ($Size !== null ? "({$Size})" : "");
            }
            $Field[] = "COLLATE " . static::Collations[$Collation];
        } else {
            $Field[] = static::Types[$Type & ~Type::Unsigned]
                       . ($Size !== null ? "({$Size})" : "");
        }

        $Field[] = $Nullable ? DataProvider::$NULL : "NOT " . DataProvider::$NULL;

        if($Default !== "") {
            $Field[] = "DEFAULT " . DataProvider::Sanitize($Default);
        }
        if($AutoIncrement) {
            $Field[] = "IDENTITY (1, 1)";
        }
        if($OnUpdate !== null) {
            $Field[] = "ON UPDATE {$OnUpdate}";
        }

        return \implode(" ", $Field);

    }

    /**
     * Creates a MsSQL conform table index.
     *
     * @param string $Name   The name of the index.
     * @param array  $Fields The fields of the index.
     * @param bool   $Unique Flag indicating whether the index is unique.
     *
     * @return string A MsSQL conform table index.
     */
    public static function Index(string $Name, bool $Unique, array $Fields): string {

        if($Name === "Primary") {
            $Index = "PRIMARY KEY";
        } else if($Unique) {
            $Index = "UNIQUE INDEX {$Name}";
        } else {
            $Index = "INDEX {$Name}";
        }

        $Transformed = [];
        foreach($Fields as $Key => $Field) {
            if(\is_string($Key)) {
                $Transformed[] = DataProvider::EscapeField($Key) . " ({$Field})";
            } else {
                $Transformed[] = DataProvider::EscapeField($Field);
            }
        }

        return $Index . " (" . \implode(", ", $Transformed) . ")";
    }
}