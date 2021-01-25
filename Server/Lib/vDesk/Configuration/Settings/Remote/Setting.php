<?php
declare(strict_types=1);

namespace vDesk\Configuration\Settings\Remote;

use vDesk\Data\IDataView;
use vDesk\Data\IManagedModel;
use vDesk\Struct\Extension;
use vDesk\Struct\Properties;
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
 */
class Setting implements IManagedModel {
    
    use Properties;
    
    /**
     * Initializes a new instance of the Setting class.
     *
     * @param string|null       $Tag   Initializes the Setting with the specified tag.
     * @param mixed|null        $Value Initializes the Setting with the specified value.
     * @param string            $Type  Initializes the Setting with the specified type.
     * @param bool              $Nullable
     * @param bool              $Public
     * @param null|array|object $Validator
     */
    public function __construct(
        protected ?string $Tag = null,
        protected mixed $Value = null,
        protected string $Type = Type::Mixed,
        protected bool $Nullable = false,
        protected bool $Public = false,
        protected array|object|null $Validator = null
    ) {
        $this->Value = match ($Type) {
            Type::Int => (int)$Value,
            Type::Float => (float)$Value,
            Type::String,
            Extension\Type::Enum,
            Extension\Type::Email,
            Extension\Type::URL,
            Extension\Type::Color,
            Extension\Type::TimeSpan => (string)$Value,
            Type::Bool => (bool)$Value,
            \DateTime::class => new \DateTime($Value)
        };
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
                ]
            ]
        );
    }
    
    /**
     * @inheritDoc
     */
    public static function FromDataView(mixed $DataView): IDataView {
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