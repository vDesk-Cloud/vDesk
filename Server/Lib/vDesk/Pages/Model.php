<?php
declare(strict_types=1);

namespace vDesk\Pages;

use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\DataProvider;
use vDesk\DataProvider\Expression;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Properties;
use vDesk\Struct\Property;
use vDesk\Struct\Text;
use vDesk\Struct\InvalidOperationException;

\define("§WHERE", "WHERE", true);

\define("§AND", "&&", true);

\define("§OR", "OR", true);

\define("§XOR", "XOR", true);

\define("§NOT", "NOT", true);

\define("§GREATERTHAN", ">", true);

\define("§LESSERTHAN", "<", true);

\define("§EQUALS", "=", true);

\define("§EQUALSNOT", "!=", true);

\define("§LIKE", "LIKE", true);

\define("§LIMIT", "LIMIT", true);

\define("§GROUPBY", "GROUPBY", true);

\define("§ORDERBY", "ORDERBY", true);

/**
 * Abstract base class for MVC-Models.
 * Note: This class is just a 'basic' generic implementation of a database-backed Model.
 * Typesafety for Properties/Fields can't be guaranteed.
 *
 * @property int $ID (write once) Gets or sets the ID of the Model.
 * @author  Kerry Holz <k.holz@artforge.eu>.
 */
abstract class Model implements IModel {
    
    use Properties;
    
    /**
     * The database-table of the Model.
     *
     * @var string
     */
    protected static string $Table = "";
    
    /**
     * The table of the Model.
     */
    protected const Table = "";
    
    /**
     * The ID of the Model.
     *
     * @var int
     */
    protected ?int $ID = null;
    
    /**
     * Initializes a new instance of the Model class.
     *
     * @param int  $ID       The ID of the model.
     * @param bool $AutoFill Determines wheter the Model should be filled by creation.
     */
    public function __construct(int $ID = null, bool $AutoFill = false) {
        $this->ID = $ID;
        $this->AddProperty("ID",
            [
                \Get => function(): ?int {
                    return $this->ID;
                },
                \Set => function(int $Value): void {
                    $this->ID = $this->ID ?? $Value;
                }
            ]);
        if($AutoFill) {
            $this->Fill();
        }
    }
    
    /**
     * @inheritDoc
     * @throws \vDesk\Struct\InvalidOperationException
     */
    public function Save(): void {
        if($this->ID === null) {
            $this->ID = Expression::Insert()
                                  ->Into(static::Table)
                                  ->Values(
                                      \array_combine(
                                          \array_keys($this->Properties),
                                          \array_map(static fn($Property) => ($Property->Getter)(), \array_values($this->Properties))
                                      )
                                  )
                                  ->ID();
        } else {
            $Properties = \array_filter($this->Properties, static fn(string $Property): bool => $Property !== "ID");
            Expression::Update(static::Table)
                      ->Set(
                          \array_combine(
                              \array_keys($Properties),
                              \array_map(static fn(Property $Property) => ($Property->Getter)(), \array_values($Properties))
                          )
                      )
                      ->Where(["ID" => $this->ID()])
                      ->Execute();
        }
    }
    
    /**
     * @inheritDoc
     */
    public function Delete(): void {
        if($this->ID !== null) {
            DataProvider\Expression::Delete()
                                   ->From(static::Table)
                                   ->Where(["ID" => $this->ID()])
                                   ->Execute();
        }
    }
    
    /**
     * Fills the Model with it's values from the database.
     *
     * @return \vDesk\Pages\Model The current instance for further chaining.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Model is virtual.
     */
    public function Fill(): Model {
        if($this->ID === null) {
            throw new IDNullException();
        }
        
        $Model = Expression::Select("*")
                           ->From(static::Table)
                           ->Where(["ID" => $this->ID()])
                           ->Execute()
                           ->ToMap();
        
        foreach(
            \array_filter(\array_keys($this->Properties), static fn(string $Name): bool => $Name !== "ID")
            as
            $Key
        ) {
            if(isset($Model[$Key])) {
                //Call Setter
                ($this->Properties[$Key]->Setter)($Model[$Key]);
            }
        }
        
        return $this;
    }
    
    /**
     * Fetches a collection of Models matching the specified search criteria.
     *
     * @param array $Criteria An array of search definitions.
     *
     * @return \vDesk\Struct\Collections\Collection A Collection containing all Models that match the specified search criteria.
     */
    public static function Find(array $Criteria): Collection {
        $SQL = "";
        foreach($Criteria as $sStatement => $amDefinitions) {
            if(\is_array($amDefinitions) && isset($amDefinitions[0]) && isset($amDefinitions[1]) && isset($amDefinitions[2])) {
                $SQL .= " $sStatement {$amDefinitions[0]} {$amDefinitions[1]} '" . DataProvider::Escape((string)$amDefinitions[2]) . "'";
            } else if(is_scalar($amDefinitions)) {
                $SQL .= " $sStatement " . DataProvider::Escape((string)$amDefinitions) . "";
            }
        }
        
        $oCollection = new Collection();
        foreach(DataProvider::Execute("SELECT * FROM " . static::$Table . " {$SQL};", false) as $oRow) {
            $oModel = new static(isset($oRow["ID"]) ? (int)$oRow["ID"] : null);
            unset($oRow["ID"]);
            foreach($oRow as $sField => $sValue) {
                try {
                    $oModel->{$sField} = DataProvider::Unescape($sValue);
                } catch(\Throwable $e) {
                    continue;
                }
            }
            $oCollection->Add($oModel);
        }
        
        return $oCollection;
    }
    
}

