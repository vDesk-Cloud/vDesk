<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider;

/**
 * Trait for table related MySQL IExpressions providing functionality for creating fields and indexes.
 *
 * @package vDesk\DataProvider\Expression\Table
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
abstract class Table {
    
    /**
     * The types of the Table\MariaDB.
     */
    public const Collations = [
        DataProvider\Collation::ASCII                                  => "ascii_general_ci",
        DataProvider\Collation::ASCII | DataProvider\Collation::Binary => "ascii_bin",
        DataProvider\Collation::UTF8                                   => "utf8mb4_unicode_ci",
        DataProvider\Collation::UTF8 | DataProvider\Collation::Binary  => "utf8mb4_bin",
        DataProvider\Collation::UTF16                                  => "utf16_unicode_ci",
        DataProvider\Collation::UTF16 | DataProvider\Collation::Binary => "utf16_bin",
        DataProvider\Collation::UTF32                                  => "utf32_unicode_ci",
        DataProvider\Collation::UTF32 | DataProvider\Collation::Binary => "utf32_bin"
    ];
    
    /**
     * The types of the Table\MariaDB.
     */
    public const Types = [
        DataProvider\Type::TinyInt    => "TINYINT",
        DataProvider\Type::SmallInt   => "SMALLINT",
        DataProvider\Type::Int        => "INT",
        DataProvider\Type::BigInt     => "BIGINT",
        DataProvider\Type::Boolean    => "TINYINT(1)",
        DataProvider\Type::Decimal    => "DECIMAL",
        DataProvider\Type::Float      => "FLOAT",
        DataProvider\Type::Double     => "Double",
        DataProvider\Type::Char       => "CHAR",
        DataProvider\Type::VarChar    => "VARCHAR",
        DataProvider\Type::TinyText   => "TINYTEXT",
        DataProvider\Type::Text       => "TEXT",
        DataProvider\Type::MediumText => "MEDIUMTEXT",
        DataProvider\Type::LongText   => "LONGTEXT",
        DataProvider\Type::Timestamp  => "TIMESTAMP",
        DataProvider\Type::Date       => "DATE",
        DataProvider\Type::Time       => "TIME",
        DataProvider\Type::DateTime   => "DATETIME",
        DataProvider\Type::TinyBlob   => "TINYBLOB",
        DataProvider\Type::Blob       => "BLOB",
        DataProvider\Type::MediumBlob => "MEDIUMBLOB",
        DataProvider\Type::LongBlob   => "LONGBLOB"
    ];
    
    /**
     * Creates a MySQL conform table field.
     *
     * @param string      $Name          The name of the table field.
     * @param int         $Type          The type of the table field.
     * @param int|null    $Size          The size of the table field.
     * @param int         $Collation     The collation of the table field.
     * @param bool        $Nullable      Flag indicating whether the table field is nullable.
     * @param string|null $Default       The default value of the table field.
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
        $Default = "",
        bool $AutoIncrement = false,
        ?string $OnUpdate = null
    ): string {
        
        $Field = [DataProvider::EscapeField($Name)];
        
        //Create type and unsigned attribute.
        $Field[] = static::Types[$Type & ~DataProvider\Type::Unsigned]
            . ($Size !== null ? "({$Size})" : "")
            . ((($Type & DataProvider\Type::Unsigned) || ($Type & DataProvider\Type::Boolean)) ? " UNSIGNED" : "");
        
        //Create collation/charset.
        if($Collation !== null) {
            if($Collation & DataProvider\Collation::ASCII) {
                $Field[] = "CHARACTER SET ascii" . (($Collation & DataProvider\Collation::Binary) ? " COLLATE ascii_bin" : "");
            } else {
                $Field[] = "COLLATE " . static::Collations[$Collation];
            }
        }
        
        $Field[] = $Nullable ? DataProvider::$NULL : "NOT " . DataProvider::$NULL;
        
        if($Default !== "") {
            $Field[] = "Default " . DataProvider::Sanitize($Default);
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
     * @param string $Name   The name of the index.
     * @param array  $Fields The fields of the index.
     * @param bool   $Unique Flag indicating whether the index is unique.
     *
     * @return string A MySQL conform table index.
     */
    public static function Index(string $Name, array $Fields, bool $Unique = false): string {
        
        $Index = [];
        
        if($Name === "Primary") {
            $Index[] = "PRIMARY KEY";
        } else if($Unique) {
            $Index[] = "UNIQUE INDEX {$Name}";
        } else {
            $Index[] = "INDEX {$Name}";
        }
        
        $Transformed = [];
        foreach($Fields as $Key => $Field) {
            if(\is_string($Key)) {
                $Transformed[] = DataProvider::EscapeField($Key) . " ({$Field})";
            } else {
                $Transformed[] = DataProvider::EscapeField($Field);
            }
        }
        $Index[] = "(" . \implode(", ", $Transformed) . ")";
        
        return \implode(" ", $Index);
    }
}