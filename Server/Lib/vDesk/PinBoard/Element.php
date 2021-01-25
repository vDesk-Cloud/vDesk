<?php
declare(strict_types=1);

namespace vDesk\PinBoard;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IModel;
use vDesk\Security\User;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents an abstract baseclass for pinboard elements.
 *
 * @property int                  $ID       Gets or sets the ID of the Element.
 * @property \vDesk\Security\User $Owner    Gets or sets the owner of the Element.
 * @property int                  $X        Gets or sets the horizontal position of the Element.
 * @property int                  $Y        Gets or sets the vertical position of the Element.
 * @package vDesk\PinBoard
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @todo    Wonder how long 2-byte integers will last until screen sizes getting bigger than 32k pixels.
 * @todo    Implement coordinate system which translates a range from 0 - 1000 to the available window size (or control parent control size?) for x/y positions.
 * @todo    Implement coordinate system which translates a range from 0 - 1000 to the available window size (or control parent control size?) for width/height.
 * @todo    Alternatively fuck off and let the pinboard scroll on overflow. 0 points stay the same top/left corner on every system.
 */
abstract class Element implements IModel {
    
    use Properties;
    
    /**
     * The database-table according the Element.
     *
     * @var string
     */
    protected static string $Table;
    
    /**
     * The table of the Element.
     */
    protected const Table = "";
    
    /**
     * Flag indicating whether the value of the X-coordinate of the position of the Element have been changed.
     *
     * @var bool
     */
    protected bool $XChanged = false;
    
    /**
     * Flag indicating whether the value of the Y-coordinate of the position of the Element have been changed.
     *
     * @var bool
     */
    protected bool $YChanged = false;
    
    /**
     * Initializes a new instance of the Element class.
     *
     * @param int|null                  $ID    Initializes the Element with the specified ID.
     * @param null|\vDesk\Security\User $Owner Initializes the Element with the specified owner.
     * @param int|null                  $X     Initializes the Element with the specified x position.
     * @param int|null                  $Y     Initializes the Element with the specified y position.
     */
    public function __construct(
       protected ?int $ID = null,
       protected ?User $Owner = null,
       protected ?int $X = null,
       protected ?int $Y = null
    ) {
        $this->AddProperties([
            "ID"    => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Owner" => [
                \Get => MappedGetter::Create(
                    $this->Owner,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("Owner")
                              ->From(static::Table)
                ),
                \Set => fn(User $Value) => $this->Owner ??= $Value
            ],
            "X"     => [
                \Get => MappedGetter::Create(
                    $this->X,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("X")
                              ->From(static::Table)
                ),
                \Set => MappedSetter::Create(
                    $this->X,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->XChanged
                )
            ],
            "Y"     => [
                \Get => MappedGetter::Create(
                    $this->Y,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Y")
                              ->From(static::Table)
                ),
                \Set => MappedSetter::Create(
                    $this->Y,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->YChanged
                )
            ]
        ]);
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->ID;
    }
    
}
