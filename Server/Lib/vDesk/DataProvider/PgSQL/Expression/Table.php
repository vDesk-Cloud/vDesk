<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;

/**
 * Trait for table related PgSQL IExpressions providing functionality for creating fields and indexes.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Table {

    /**
     * The types of the Table\MariaDB.
     */
    public const Collations = [
        Collation::ASCII                     => "en_US.utf8",
        Collation::ASCII | Collation::Binary => "en_US.utf8",
        Collation::UTF8                      => "en_US.utf8",
        Collation::UTF8 | Collation::Binary  => "en_US.utf8",
        Collation::UTF16                     => "en_US.utf8",
        Collation::UTF16 | Collation::Binary => "en_US.utf8",
        Collation::UTF32                     => "en_US.utf8",
        Collation::UTF32 | Collation::Binary => "en_US.utf8"
    ];

    /**
     * The types of the Table\MariaDB.
     */
    public const Types = [
        Type::TinyInt    => "SMALLINT",
        Type::SmallInt   => "SMALLINT",
        Type::Int        => "INTEGER",
        Type::BigInt     => "BIGINT",
        Type::Boolean    => "BOOLEAN",
        Type::Decimal    => "DECIMAL",
        Type::Float      => "REAL",
        Type::Double     => "DOUBLE PRECISION",
        Type::Char       => "CHAR",
        Type::VarChar    => "VARCHAR",
        Type::TinyText   => "VARCHAR(255)",
        Type::Text       => "VARCHAR(65535)",
        Type::MediumText => "VARCHAR(16777215)",
        Type::LongText   => "TEXT",
        Type::Timestamp  => "TIMESTAMP",
        Type::Date       => "DATE",
        Type::Time       => "TIME",
        Type::DateTime   => "TIMESTAMPTZ",
        Type::TinyBlob   => "BYTEA(255)",
        Type::Blob       => "BYTEA(65535)",
        Type::MediumBlob => "BYTEA(16777215)",
        Type::LongBlob   => "BYTEA(4294967295)"
    ];

    /**
     * Creates a MySQL conform table field.
     *
     * @param string      $Name          The name of the table field.
     * @param int         $Type          The type of the table field.
     * @param int|null    $Size          The size of the table field.
     * @param bool        $Nullable      Flag indicating whether the table field is nullable.
     * @param string      $Default       The default value of the table field.
     * @param bool        $AutoIncrement The size of the table field.
     * @param string|null $OnUpdate      The size of the table field.
     *
     * @return string A PgSQL conform table field.
     */
    public static function Field(
        string $Name,
        int $Type,
        ?int $Size = null,
        bool $Nullable = false,
        $Default = "",
        bool $AutoIncrement = false,
        ?string $OnUpdate = null
    ): string {

        $Field = [DataProvider::EscapeField($Name)];

        if($AutoIncrement) {
            $Field[] = match (static::Types[$Type & ~Type::Unsigned]) {
                static::Types[Type::SmallInt], static::Types[Type::TinyInt] => "SMALLSERIAL",
                static::Types[Type::Int] => "SERIAL",
                static::Types[Type::BigInt] => "BIGSERIAL"
            };
        } else {
            //Create type and unsigned attribute.
            $Field[] = static::Types[$Type & ~DataProvider\Type::Unsigned]
                       . ($Size !== null ? "({$Size})" : "")
                       . (($Type & DataProvider\Type::Unsigned) || ($Type & Type::Boolean) ? " UNSIGNED" : "");
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

        //Postgres doesn't support limits on indices.
        $Transformed = [];
        foreach($Fields as $Key => $Field) {
            $Transformed[] = \is_string($Key) ? DataProvider::EscapeField($Key) : DataProvider::EscapeField($Field);
        }

        return $Index . " (" . \implode(", ", $Transformed) . ")";
    }
}