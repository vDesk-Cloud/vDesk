<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider;

/**
 * Utility class for table related MsSQL Expressions providing functionality for creating fields and indexes.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Table {

    /**
     * The types of the Table.
     */
    public const Collations = [
        DataProvider\Collation::ASCII                                  => "en_US.utf8",
        DataProvider\Collation::ASCII | DataProvider\Collation::Binary => "en_US.utf8",
        DataProvider\Collation::UTF8                                   => "en_US.utf8",
        DataProvider\Collation::UTF8 | DataProvider\Collation::Binary  => "en_US.utf8",
        DataProvider\Collation::UTF16                                  => "en_US.utf8",
        DataProvider\Collation::UTF16 | DataProvider\Collation::Binary => "en_US.utf8",
        DataProvider\Collation::UTF32                                  => "en_US.utf8",
        DataProvider\Collation::UTF32 | DataProvider\Collation::Binary => "en_US.utf8"
    ];

    /**
     * The types of the Table.
     */
    public const Types = [
        DataProvider\Type::TinyInt    => "SMALLINT",
        DataProvider\Type::SmallInt   => "SMALLINT",
        DataProvider\Type::Int        => "INTEGER",
        DataProvider\Type::BigInt     => "BIGINT",
        DataProvider\Type::Boolean    => "BOOLEAN",
        DataProvider\Type::Decimal    => "DECIMAL",
        DataProvider\Type::Float      => "REAL",
        DataProvider\Type::Double     => "DOUBLE PRECISION",
        DataProvider\Type::Char       => "CHAR",
        DataProvider\Type::VarChar    => "VARCHAR",
        DataProvider\Type::TinyText   => "VARCHAR(255)",
        DataProvider\Type::Text       => "VARCHAR(65535)",
        DataProvider\Type::MediumText => "VARCHAR(16777215)",
        DataProvider\Type::LongText   => "TEXT",
        DataProvider\Type::Timestamp  => "TIMESTAMP",
        DataProvider\Type::Date       => "DATE",
        DataProvider\Type::Time       => "TIME",
        DataProvider\Type::DateTime   => "TIMESTAMPTZ",
        DataProvider\Type::TinyBlob   => "BYTEA(255)",
        DataProvider\Type::Blob       => "BYTEA(65535)",
        DataProvider\Type::MediumBlob => "BYTEA(16777215)",
        DataProvider\Type::LongBlob   => "BYTEA(4294967295)"
    ];

    /**
     * Creates a MsSQL conform table field.
     *
     * @param string      $Name          The name of the table field.
     * @param int         $Type          The type of the table field.
     * @param int|null    $Size          The size of the table field.
     * @param null|int    $Collation     The collation of the table field.
     * @param bool        $Nullable      Flag indicating whether the table field is nullable.
     * @param string      $Default       The default value of the table field.
     * @param bool        $AutoIncrement The size of the table field.
     * @param string|null $OnUpdate      The size of the table field.
     *
     * @return string A MsSQL conform table field.
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

        if($AutoIncrement) {
            $Field[] = match (static::Types[$Type & ~DataProvider\Type::Unsigned]) {
                static::Types[DataProvider\Type::SmallInt], static::Types[DataProvider\Type::TinyInt] => "SMALLSERIAL",
                static::Types[DataProvider\Type::Int] => "SERIAL",
                static::Types[DataProvider\Type::BigInt] => "BIGSERIAL"
            };
        } else {
            //Create type and unsigned attribute.
            $Field[] = static::Types[$Type & ~DataProvider\Type::Unsigned]
                       . ($Size !== null ? "({$Size})" : "")
                       . ((($Type & DataProvider\Type::Unsigned) || ($Type & DataProvider\Type::Boolean)) ? " UNSIGNED" : "");
        }

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
     * @param string $Table  Optional table name for PostgreSQL indices.
     *
     * @return string A MySQL conform table index.
     */
    public static function Index(string $Name, array $Fields, bool $Unique = false, string $Table = ""): string {

        if($Name === "Primary") {
            $Index = "PRIMARY KEY";
        } else if($Unique) {
            $Index = "UNIQUE";
        } else {
            $Index = \rtrim("CREATE INDEX {$Name}") . " ON {$Table}";
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