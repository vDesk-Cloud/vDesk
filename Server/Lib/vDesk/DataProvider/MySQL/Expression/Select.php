<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider\MySQL\Expression;
use vDesk\DataProvider;
use vDesk\DataProvider\Expression\ISelect;

/**
 * Represents a MySQL compatible SELECT SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Select
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Select implements ISelect {
    
    /**
     * The SQL-statement of the Select.
     *
     * @var string
     */
    private string $Statement = "";
    
    /**
     * The last join alias of the Select.
     *
     * @var string[]
     */
    private array $Aliases = [];
    
    /**
     * @inheritDoc
     */
    public function __construct(...$Fields) {
        
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
    public function Distinct(...$Fields): self {
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
    public function From(...$Tables): self {
        
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
            $this->Aliases[]   = \substr($Table, \strrpos($Table, ".") + 1);
            $FlattenedTables[] = DataProvider::SanitizeField($Table);
        }
        
        $this->Statement .= "FROM " . \implode(", ", $FlattenedTables) . " ";
        
        return $this;
        
    }
    
    /**
     * @inheritDoc
     */
    public function Where(array ...$Conditions): self {
        $this->Statement .= "WHERE " . Expression::TransformConditions($this->Aliases, ...$Conditions) . " ";
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Execute($Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }
    
    /**
     * @inheritDoc
     */
    public function __toString() {
        return $this->Statement;
    }
    
    /**
     * @inheritDoc
     */
    public function InnerJoin(string $Table, string $Alias = null): self {
        $this->Aliases[] = $Alias ?? \substr($Table, \strrpos($Table, ".") + 1);
        $this->Statement .= "INNER JOIN " . DataProvider::SanitizeField($Table) . " " . ($Alias !== null ? "AS " . DataProvider::EscapeField($Alias) . " " : "");
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function On(array ...$Fields): self {
        $this->Statement .= "ON " . Expression::TransformConditions($this->Aliases, ...$Fields) . " ";
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Limit(int $Amount): self {
        $this->Statement .= "LIMIT {$Amount} ";
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Offset(int $Index): self {
        $this->Statement .= "OFFSET {$Index} ";
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function RightJoin(string $Table, string $Alias = null): self {
        $this->Aliases[] = $Alias ?? \substr($Table, \strrpos($Table, ".") + 1);
        $this->Statement .= "RIGHT JOIN " . DataProvider::SanitizeField($Table) . " " . ($Alias !== null ? "AS " . DataProvider::EscapeField($Alias) . " " : "");
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function LeftJoin(string $Table, string $Alias = null): self {
        $this->Aliases[] = $Alias ?? \substr($Table, \strrpos($Table, ".") + 1);
        $this->Statement .= "LEFT JOIN " . DataProvider::SanitizeField($Table) . " " . ($Alias !== null ? "AS " . DataProvider::EscapeField($Alias) . " " : "");
        return $this;
        
    }
    
    /**
     * @inheritDoc
     */
    public function getIterator(): IResult {
        return $this->Execute();
    }
    
    /**
     * @inheritDoc
     */
    public function __invoke() {
        return $this->Execute()->ToValue();
    }
    
    /**
     * @inheritDoc
     */
    public function OrderBy(array $Fields): self {
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
    public function Union(ISelect $Select, bool $ALL = false): self {
        $this->Statement .= "UNION " . ($ALL ? "ALL " : "") . (string)$Select;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Exists(ISelect $Select): self {
        $this->Statement .= "EXISTS ($Select)";
        return $this;
    }
}