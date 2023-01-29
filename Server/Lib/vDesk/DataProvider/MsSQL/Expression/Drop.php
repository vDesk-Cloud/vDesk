<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider;

/**
 * Represents a MsSQL compatible "DROP" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Drop extends \vDesk\DataProvider\AnsiSQL\Expression\Drop {

    /**
     * Flag indicating whether a primary index should be dropped.
     *
     * @var bool
     */
    protected bool $Primary = false;

    /** @inheritDoc */
    public function Index(string $Name): static {
        if($Name === "Primary"){
            $this->Primary = true;
            return $this;
        }
        return parent::Index($Name);
    }

    /** @inheritDoc */
    public function On(string $Table): static {
        if($this->Primary){
            $this->Statement =  <<<Statement
DECLARE @Name NVARCHAR(512), @Statement NVARCHAR(MAX);
SELECT 
    @Name = name
FROM
    sys.key_constraints
WHERE
    [type] = 'PK'
AND 
    [parent_object_id] = Object_id('$Table'); 
Statement;
            $this->Statement .= "SELECT @Statement = 'ALTER TABLE " . DataProvider::SanitizeField($Table). " DROP CONSTRAINT ' + @Name + ';' ";
            $this->Statement .= "EXEC sp_executeSQL @Statement;";
            return $this;
        }
        return parent::On($Table);
    }
    
}