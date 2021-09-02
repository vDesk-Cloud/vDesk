<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL;

use vDesk\DataProvider\IProvider;
use vDesk\DataProvider\IResult;
use vDesk\Data\IManagedModel;
use vDesk\Struct\Type;

/**
 * Abstract data-provider for MySQL and MariaDB databases.
 *
 * @package vDesk\DataProvider\Provider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Provider implements IProvider {

    /**
     * Regular expression to extract the table- and column name of a field descriptor.
     */
    public const SeparatorExpression = "/^(\w+)\.(\w+)$/";

    /**
     * The separator character indicating database-, schema-, table- and column name of a field descriptor.
     */
    public const Separator = ".";

    /**
     * The format for storing \DateTime values in a MySQL conform format.
     */
    public const Format = "Y-m-d\TH:i:s";

    /**
     * The reserved key words of the Provider.
     */
    public const Reserved = [
        "ALL",
        "ANALYSE",
        "ANALYZE",
        "AND",
        "ANY",
        "ARRAY",
        "AS",
        "ASC",
        "ASYMMETRIC",
        "AUTHORIZATION",
        "BETWEEN",
        "BOTH",
        "CASE",
        "BINARY",
        "CAST",
        "CHECK",
        "COLLATE",
        "COLUMN",
        "CREATE",
        "CROSS",
        "CURRENT_DATE",
        "CURRENT_ROLE",
        "CURRENT_TIME",
        "CURRENT_TIMESTAMP",
        "DEFAULT",
        "DEFERRABLE",
        "DESC",
        "DISTINCT",
        "DO",
        "ELSE",
        "END",
        "EXCEPT",
        "FALSE",
        "FOR",
        "FOREIGN",
        "FROM",
        "GRANT",
        "GROUP",
        "HAVING",
        "IN",
        "INITIALLY",
        "INNER",
        "INTERSECT",
        "INTO",
        "IS",
        "ISNULL",
        "JOIN",
        "LEADING",
        "LEFT",
        "LIKE",
        "LIMIT",
        "LOCALTIME",
        "LOCALTIMESTAMP",
        "NATURAL",
        "NEW",
        "NOT",
        "NOTNULL",
        "NULL",
        "OFF",
        "OFFSET",
        "OLD",
        "ON",
        "ONLY",
        "OR",
        "ORDER",
        "OUTER",
        "OVERLAPS",
        "PLACING",
        "PRIMARY",
        "REFERENCES",
        "RIGHT",
        "SELECT",
        "SESSION_USER",
        "SIMILAR",
        "SOME",
        "SYMMETRIC",
        "TABLE",
        "THEN",
        "TO",
        "TRAILING",
        "TRUE",
        "UNION",
        "UNIQUE",
        "USER",
        "USING",
        "VERBOSE",
        "WHEN",
        "WHERE"
    ];

    /**
     * Enumeration of characters to escape within SQL statements.
     */
    public const Escape = ["\"", "'", "`", "\\", "/", "\b", "\f", "\b", "\r", "\t", "\u0000", "\u0001", "\u001f"];

    /**
     * Enumeration of escaped control characters.
     */
    public const Escaped = ["\\\"", "\\'", "\\`", "\\\\", "\\/", "\\b", "\\f", "\\b", "\\r", "\\t", "\\u0000", "\\u0001", "\\u001f"];

    /**
     * Database null value.
     */
    public const NULL = "NULL";

    /**
     * Executes a stored procedure on the SQL-server.
     *
     * @param string $Procedure The name of the procedure to execute.
     * @param array  $Arguments The list of arguments to pass to the procedure.
     *
     * @return \vDesk\DataProvider\MySQL\Result The IResult containing the results of the executed procedure.
     */
    public function Call(string $Procedure, array $Arguments): IResult {
        return $this->Execute("CALL {$Procedure}(" . (\count($Arguments) > 0 ? "'" . implode("','", $Arguments) . "'" : "") . ");", true);
    }

    /**
     * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
     *
     * @param string $String The string to escape.
     *
     * @return string The escaped string.
     */
    public function Escape(string $String): string {
        return \str_replace(static::Escape, static::Escaped, $String);
    }

    /**
     * Escapes reserved words in a field according to the current database-specification.
     *
     * @param string $Field The field to escape.
     *
     * @return string The escaped field.
     */
    public function EscapeField(string $Field): string {
        return \in_array(\strtoupper(\trim($Field)), static::Reserved)
            ? "'{$Field}'"
            : $Field;
    }

    /**
     * Sanitizes a value according to the AnsiSQL database-specification.
     *
     * @param mixed|\vDesk\Data\IManagedModel $Value The value to sanitize.
     *
     * @return string|int The sanitized value.
     */
    public function Sanitize(mixed $Value): string|int {
        if($Value instanceof IManagedModel) {
            return $this->Sanitize($Value->ID());
        }
        return match (Type::Of($Value)) {
            Type::String => "'{$this->Escape($Value)}'",
            Type::Bool, Type::Boolean => (int)$Value,
            Type::Null => static::NULL,
            Type::Object, Type::Array => "'" . \json_encode($Value) . "'",
            \DateTime::class => "'{$Value->format(static::Format)}'",
            default => (string)$Value
        };
    }

    /**
     * Sanitizes reserved words in a field according to the AnsiSQL database-specification.
     *
     * @param string $Field The field to sanitize.
     *
     * @return string The sanitized field.
     */
    public function SanitizeField(string $Field): string {
        $Matches = [];
        return (int)\preg_match(static::SeparatorExpression, $Field, $Matches) > 0
            ?  $this->EscapeField($Matches[1])
              . static::Separator . $this->EscapeField($Matches[2])
            : $this->EscapeField($Field);
    }

}