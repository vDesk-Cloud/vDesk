<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider\AnsiSQL\Expression;
use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;
use vDesk\DataProvider\Expression\ISelect;

/**
 * Abstract base class for AnsiSQL compatible "SELECT" Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Select implements ISelect {

    /**
     * The SQL-statement of the Select.
     *
     * @var string
     */
    protected string $Statement = "";

    /**
     * The last join alias of the Select.
     *
     * @var string[]
     */
    protected array $Aliases = [];

    /**
     * @inheritDoc
     */
    public function __construct(string|array|IAggregateFunction ...$Fields) {

        $FlattenedFields = [];

        foreach($Fields as $Field) {
            //Check if an alias has been passed.
            if(\is_array($Field)) {
                //Check if an IAggregateFunction has been passed.
                if(isset($Field[0]) && $Field[0] instanceof IAggregateFunction) {
                    $FlattenedFields[] = (string)$Field[0] . " AS " . DataProvider::EscapeField($Field[1]);
                    continue;
                }
                $FlattenedFields[] = DataProvider::SanitizeField(\key($Field)) . " AS " . DataProvider::EscapeField(\current($Field));
                continue;
            }
            //Check if an IAggregateFunction has been passed.
            if($Field instanceof IAggregateFunction) {
                $FlattenedFields[] = (string)$Field;
                continue;
            }

            $FlattenedFields[] = DataProvider::SanitizeField($Field);
        }

        $this->Statement .= "SELECT " . \implode(", ", $FlattenedFields) . (\count($FlattenedFields) > 0 ? " " : "");

    }

    /**
     * @inheritDoc
     */
    public function Distinct(string|array|IAggregateFunction ...$Fields): static {
        $FlattenedFields = [];

        foreach($Fields as $Field) {
            $FlattenedFields[] = \is_array($Field)
                ? DataProvider::SanitizeField(\key($Field)) . " AS " . DataProvider::EscapeField(\current($Field))
                : DataProvider::SanitizeField($Field);
        }

        $this->Statement .= "DISTINCT " . \implode(", ", $FlattenedFields) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function From(string|array|ISelect ...$Tables): static {

        $FlattenedTables = [];

        foreach($Tables as $Table) {

            //Check if a sub select has been passed.
            if($Table instanceof ISelect) {
                $this->Aliases[] = $Alias = \next($Tables);
                $this->Statement .= "FROM ({$Table}) AS " . DataProvider::EscapeField($Alias) . " ";
                return $this;
            }

            if(\is_array($Table)) {
                $this->Aliases[]   = $Alias = \current($Table);
                $FlattenedTables[] = DataProvider::SanitizeField(\key($Table)) . " AS " . DataProvider::EscapeField($Alias);
                continue;
            }
            // Strip out any database names.
            $this->Aliases[]   = \substr($Table, \strrpos($Table, DataProvider::$Provider::Separator) + 1);
            $FlattenedTables[] = DataProvider::SanitizeField($Table);
        }

        $this->Statement .= "FROM " . \implode(", ", $FlattenedTables) . " ";

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function Where(array ...$Conditions): static {
        $this->Statement .= "WHERE " . Expression::TransformConditions($this->Aliases, ...$Conditions) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function InnerJoin(string $Table, string $Alias = null): static {
        return $this->Join("INNER", $Table, $Alias);
    }

    /**
     * @inheritDoc
     */
    public function RightJoin(string $Table, string $Alias = null): static {
        return $this->Join("RIGHT OUTER", $Table, $Alias);
    }

    /**
     * @inheritDoc
     */
    public function LeftJoin(string $Table, string $Alias = null): static {
        return $this->Join("LEFT OUTER", $Table, $Alias);
    }

    /**
     * @inheritDoc
     */
    public function FullJoin(string $Table, string $Alias = null): static {
        return $this->Join("FULL OUTER", $Table, $Alias);
    }

    /**
     * Applies a JOIN statement with a specified type.
     *
     * @param string      $Type  The type of the statement.
     * @param string      $Table The table to join.
     * @param null|string $Alias An optional alias for the table to join.
     *
     * @return $this The current instance for further chaining.
     */
    protected function Join(string $Type, string $Table, string $Alias = null): static {
        $this->Aliases[] = $Alias ?? \substr($Table, \strrpos($Table, DataProvider::$Provider::Separator) + 1);
        $this->Statement .= "{$Type} JOIN " . DataProvider::SanitizeField($Table) . " " . ($Alias !== null ? "AS " . DataProvider::EscapeField($Alias) . " " : "");
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function On(array ...$Fields): static {
        $this->Statement .= "ON " . Expression::TransformConditions($this->Aliases, ...$Fields) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Limit(int $Amount): static {
        $this->Statement .= "LIMIT {$Amount} ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Offset(int $Index): static {
        $this->Statement .= "OFFSET {$Index} ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function OrderBy(array $Fields): static {
        $Conditions = [];
        foreach($Fields as $Field => $Order) {
            if(\is_string($Order)) {
                $Conditions[] = DataProvider::SanitizeField($Order);
            } else {
                $Conditions[] = DataProvider::SanitizeField($Field) . " " . ((bool)$Order ? "ASC " : "DESC ");
            }
        }
        $this->Statement .= "ORDER BY " . \implode(", ", $Conditions);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Union(ISelect $Select, bool $ALL = false): static {
        $this->Statement .= "UNION " . ($ALL ? "ALL " : "") . (string)$Select;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Exists(ISelect $Select): static {
        $this->Statement .= "EXISTS ($Select)";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): IResult {
        return $this->Execute();
    }

    //Implementation of IExpression.

    /**
     * @inheritDoc
     */
    public function __invoke(): null|string|int|float {
        return $this->Execute()->ToValue();
    }

    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return $this->Statement;
    }

}