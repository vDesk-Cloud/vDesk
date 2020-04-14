<?php
declare(strict_types=1);

namespace vDesk\Configuration\Settings\Remote;

use vDesk\Data\IDataView;
use vDesk\Data\IManagedModel;
use vDesk\Struct\Extension;
use vDesk\Struct\Properties;
use vDesk\Struct\Property\Getter;
use vDesk\Struct\Type;

/**
 * Represents a configuration setting.
 *
 * @property string $Tag                                    Gets or sets the tag of the Setting.
 * @property mixed  $Value                                  Gets or sets the value of the Setting.
 * @property mixed  $Type                                   Gets or sets the type of the Setting.
 * @property mixed  $Nullable                               Gets or sets a value indicating whether the value of the Setting is nullable.
 * @property mixed  $Public                                 Gets or sets a value indicating whether the Setting is public visible.
 * @property mixed  $Validator                              Gets or sets the validator of the Setting.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Setting implements IManagedModel {
    
    use Properties;
    
    /**
     * The tag of the Setting.
     *
     * @var null|string
     */
    protected ?string $Tag;
    
    /**
     * The value of the Setting.
     *
     * @var null|mixed
     */
    protected $Value;
    
    /**
     * The type of the Setting.
     *
     * @var string
     */
    protected string $Type;
    
    /**
     * Flag indicating whether the value of the Setting is nullable.
     *
     * @var bool
     */
    protected bool $Nullable;
    
    /**
     * Flag indicating whether the Setting is public visible..
     *
     * @var bool
     */
    protected bool $Public;
    
    /**
     * The validator of the Setting.
     *
     * @var array|object|null
     */
    protected $Validator;
    
    /**
     * Initializes a new instance of the Setting class.
     *
     * @param string|null $Tag   Initializes the Setting with the specified tag.
     * @param mixed|null  $Value Initializes the Setting with the specified value.
     * @param string      $Type  Initializes the Setting with the specified type.
     * @param bool        $Nullable
     * @param bool        $Public
     * @param null        $Validator
     */
    public function __construct(?string $Tag = null, $Value = null, string $Type = Type::Mixed, bool $Nullable = false, bool $Public = false, $Validator = null) {
        $this->Tag       = $Tag;
        $this->Value     = $Value;
        $this->Type      = $Type;
        $this->Nullable  = $Nullable;
        $this->Public    = $Public;
        $this->Validator = $Validator;
        switch($Type) {
            case Type::Int:
                $this->Value = (int)$Value;
                break;
            case Type::Float:
                $this->Value = (float)$Value;
                break;
            case Type::String:
            case Extension\Type::Enum:
            case Extension\Type::Email:
            case Extension\Type::URL:
            case Extension\Type::Color:
            case Extension\Type::TimeSpan:
                $this->Value = (string)$Value;
                break;
            case Type::Bool:
                $this->Value = (bool)$Value;
                break;
            case \DateTime::class:
                $this->Value = new \DateTime($Value);
                break;
        }
        $this->Type = $Type;
        $this->AddProperties(
            [
                "Tag"       => [
                    \Get => fn(): ?string => $this->Tag
                
                ],
                "Value"     => [
                    \Get => fn() => $this->Value
                ],
                "Type"      => [
                    \Get => fn(): ?string => $this->Type
                ],
                "Nullable"  => [
                    \Get => fn(): ?bool => $this->Nullable
                ],
                "Public"    => [
                    \Get => fn(): ?bool => $this->Public
                ],
                "Validator" => [
                    \Get => fn() => $this->Validator
                ],
            ]
        );
    }
    
    /**
     * @inheritDoc
     */
    public static function FromDataView($DataView): IDataView {
        // TODO: Implement FromDataView() method.
    }
    
    /**
     * @inheritDoc
     */
    public function ID() {
        // TODO: Implement ID() method.
    }
    
    /**
     * @inheritDoc
     */
    public function Fill(): IManagedModel {
        // TODO: Implement Fill() method.
    }
    
    /**
     * @inheritDoc
     */
    public function ToDataView(bool $Reference = false) {
        // TODO: Implement ToDataView() method.
    }
}