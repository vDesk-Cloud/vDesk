<?php
declare(strict_types=1);

namespace vDesk\Configuration\Settings\Remote;

use vDesk\Data\IModel;
use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Struct\Collections\Typed\Observable\Dictionary;

/**
 * Class Settings Represents a Dictionary of database stored remote Settings.
 *
 * @property-read string|null $Domain Gets the domain of the Settings.
 * @package vDesk\Configuration\Settings\Remote
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Settings extends Dictionary implements IModel {

    /**
     * The Type of the value of the Settings.
     */
    public const Type = Setting::class;

    /**
     * The domain access-key of the Settings.
     *
     * @var null|string
     */
    protected ?string $Domain;

    /**
     * The added Settings of the Settings Dictionary.
     *
     * @var array
     */
    protected array $Added = [];

    /**
     * The deleted Settings of the Settings Dictionary.
     *
     * @var array
     */
    protected array $Deleted = [];

    /**
     * Initializes a new instance of the Settings class.
     *
     * @param \vDesk\Configuration\Settings\Remote\Setting[] $Settings Initializes the Settings with the specified Dictionary of Settings.
     * @param string|null                                    $Domain   Initializes the Settings with the specified domain.
     */
    public function __construct(iterable $Settings = [], ?string $Domain = null) {
        parent::__construct($Settings);
        $this->Dispatching(false);
        $this->OnAdd[]    = fn(Setting $Setting) => $this->Added[] = $Setting;
        $this->OnDelete[] = function(Setting $Setting) {
            if(!\in_array($Setting, $this->Added)) {
                $this->Deleted[] = $Setting;
            }
        };
        $this->Domain     = $Domain;
        $this->AddProperty("Domain", [\Get => fn(): ?string => $this->Domain]);
        $this->Dispatching(true);
    }

    /**
     * Fills the Settings with the configuration settings stored in the database.
     *
     * @return \vDesk\Configuration\Settings\Remote\Settings The filled Settings.
     */
    public function Fill(): Settings {
        if($this->Domain !== null) {
            $this->Dispatching(false);
            foreach(
                Expression::Select("Tag", "Value", "Type")
                          ->From("Configuration.Settings")
                          ->Where(["Domain" => $this->Domain])
                as
                $Setting
            ) {
                $this->Add($Setting["Tag"], new Setting($Setting["Tag"], $Setting["Value"], $Setting["Type"]));
            }
            $this->Dispatching(true);
        }
        return $this;
    }

    /**
     * Saves the values of the Settings to the database.
     *
     * @param bool $Create Flag indicating whether to create a new set of Settings instead of updating existing Settings.
     */
    public function Save(bool $Create = false): void {
        if($Create) {
            foreach($this as $Setting) {
                Expression::Insert()
                          ->Into("Configuration.Settings")
                          ->Values(
                              [
                                  "Domain"    => $this->Domain,
                                  "Tag"       => $Setting->Tag,
                                  "Value"     => $Setting->Value,
                                  "Type"      => $Setting->Type,
                                  "Nullable"  => $Setting->Nullable,
                                  "Public"    => $Setting->Public,
                                  "Validator" => $Setting->Validator
                              ]
                          )
                          ->Execute();
            }
        } else {
            foreach($this->Added as $Setting) {
                Expression::Insert()
                          ->Into("Configuration.Settings")
                          ->Values(
                              [
                                  "Domain"    => $this->Domain,
                                  "Tag"       => $Setting->Tag,
                                  "Value"     => $Setting->Value,
                                  "Type"      => $Setting->Type,
                                  "Nullable"  => $Setting->Nullable,
                                  "Public"    => $Setting->Public,
                                  "Validator" => $Setting->Validator
                              ]
                          )
                          ->Execute();
            }

            /** @var \vDesk\Configuration\Settings\Remote\Setting $Setting */
            foreach(
                $this->Filter(fn(Setting $Setting): bool => !\in_array($Setting, $this->Added))
                     ->Filter(fn(Setting $Setting): bool => !\in_array($Setting, $this->Deleted))
                as
                $Setting
            ) {
                Expression::Update("Configuration.Settings")
                          ->Set(["Value" => $Setting->Value])
                          ->Where(
                              [
                                  "Domain" => $this->Domain,
                                  "Tag"    => $Setting->Tag
                              ]
                          )
                          ->Execute();
            }
            foreach($this->Deleted as $Setting) {
                Expression::Delete()
                          ->From("Configuration.Settings")
                          ->Where(
                              [
                                  "Domain" => $this->Domain,
                                  "Tag"    => $Setting->Tag
                              ]
                          )
                          ->Execute();

            }
        }
    }

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Setting {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): Setting {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt($Key): Setting {
        return parent::RemoveAt($Key);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Tag) {
        return $this->offsetExists($Tag)
            ? $this->Elements[$Tag]->Value
            : parent::offsetGet($Tag);
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
    public function ID(): ?string {
        return $this->Domain;
    }

    /**
     * @inheritDoc
     */
    public function ToDataView(bool $Reference = false) {
        return $this->Reduce(
            static function(array $Settings, Setting $Setting): array {
                $Settings[] = $Setting->ToDataView();
                return $Settings;
            },
            []
        );
    }

    /**
     * @inheritDoc
     */
    public function Delete(): void {
        if($this->Domain !== null) {
            Expression::Delete()
                      ->From("Configuration.Settings")
                      ->Where(["Domain" => $this->Domain])
                      ->Execute();
        }
    }
}