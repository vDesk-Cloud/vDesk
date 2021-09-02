<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;

/**
 * Trait for table related MySQL IExpressions providing functionality for creating fields and indexes.
 *
 * @package vDesk\DataProvider\Expression\Table
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Table {

    /**
     * The types of the Table\MariaDB.
     */
    public const Collations = [
        Collation::ASCII                     => "ascii_general_ci",
        Collation::ASCII | Collation::Binary => "ascii_bin",
        Collation::UTF8                      => "utf8mb4_unicode_ci",
        Collation::UTF8 | Collation::Binary  => "utf8mb4_bin",
        Collation::UTF16                     => "utf16_unicode_ci",
        Collation::UTF16 | Collation::Binary => "utf16_bin",
        Collation::UTF32                     => "utf32_unicode_ci",
        Collation::UTF32 | Collation::Binary => "utf32_bin"
    ];

    /**
     * The types of the Table\MariaDB.
     */
    public const Types = [
        Type::TinyInt    => "TINYINT",
        Type::SmallInt   => "SMALLINT",
        Type::Int        => "INT",
        Type::BigInt     => "BIGINT",
        Type::Boolean    => "TINYINT(1)",
        Type::Decimal    => "DECIMAL",
        Type::Float      => "FLOAT",
        Type::Double     => "DOUBLE",
        Type::Char       => "CHAR",
        Type::VarChar    => "VARCHAR",
        Type::TinyText   => "TINYTEXT",
        Type::Text       => "TEXT",
        Type::MediumText => "MEDIUMTEXT",
        Type::LongText   => "LONGTEXT",
        Type::Timestamp  => "TIMESTAMP",
        Type::Date       => "DATE",
        Type::Time       => "TIME",
        Type::DateTime   => "DATETIME",
        Type::TinyBlob   => "TINYBLOB",
        Type::Blob       => "BLOB",
        Type::MediumBlob => "MEDIUMBLOB",
        Type::LongBlob   => "LONGBLOB"
    ];

    /**
     * Creates a MySQL conform table field.
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
     * @return string A MySQL conform table field.
     */
    public static function Field(
        string $Name,
        int $Type,
        ?int $Size = null,
        ?int $Collation = null,
        bool $Nullable = false,
        mixed $Default = "",
        bool $AutoIncrement = false,
        ?string $OnUpdate = null
    ): string {

        $Field = [DataProvider::EscapeField($Name)];

        //Create type and unsigned attribute.
        $Field[] = static::Types[$Type & ~Type::Unsigned]
                   . ($Size !== null ? "({$Size})" : "")
                   . (($Type & Type::Unsigned) || ($Type & Type::Boolean) ? " UNSIGNED" : "");

        //Create collation/charset.
        if($Collation !== null) {
            if($Collation & Collation::ASCII) {
                $Field[] = "CHARACTER SET ascii" . ($Collation & Collation::Binary ? " COLLATE ascii_bin" : "");
            } else {
                $Field[] = "COLLATE " . static::Collations[$Collation];
            }
        }

        $Field[] = $Nullable ? DataProvider::$NULL : "NOT " . DataProvider::$NULL;

        if($Default !== "") {
            $Field[] = "DEFAULT " . DataProvider::Sanitize($Default);
        }
        if($AutoIncrement) {
            $Field[] = "AUTO_INCREMENT";
        }
        if($OnUpdate !== null) {
            $Field[] = "ON UPDATE {$OnUpdate}";
        }

        return \implode(" ", $Field);

    }

    /**
     * Creates a MySQL conform table index.
     *
     * @param string[] $Fields The fields of the index.
     * @param bool     $Unique Flag indicating whether the index is unique.
     * @param string   $Name   The name of the index.
     *
     * @return string A MySQL conform table index.
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
        foreach($Fields as $Field => $Size) {
            if(\is_string($Field)) {
                $Transformed[] = DataProvider::EscapeField($Field) . " ({$Size})";
            } else {
                $Transformed[] = DataProvider::EscapeField($Size);
            }
        }

        return $Index . " (" . \implode(", ", $Transformed) . ")";
    }
}