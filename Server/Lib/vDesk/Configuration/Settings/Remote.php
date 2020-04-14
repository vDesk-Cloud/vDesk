<?php
declare(strict_types=1);

namespace vDesk\Configuration\Settings;

use vDesk\Configuration\Settings\Remote\Setting;
use vDesk\Configuration\Settings\Remote\Settings;
use vDesk\DataProvider\Expression;
use vDesk\Struct\Collections\Typed\Dictionary;

/**
 * Represents a dictionary of {@link \vDesk\Configuration\Settings\Remote\Setting} dictionaries whose values are stored in the
 * database.
 *
 * @package vDesk\Settings\Settings
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Remote extends Dictionary {

    /**
     * The type of the value of the Remote configuration.
     */
    public const Type = Settings::class;

    /**
     * @inheritdoc
     */
    public function offsetGet($Domain) {

        if($this->offsetExists($Domain)) {
            return $this->Elements[$Domain];
        }

        //Fetch settings.
        $this->Add(
            $Domain,
            new Settings(
                (static function() use ($Domain): \Generator {
                    foreach(
                        Expression::Select("Tag", "Value", "Type")
                                  ->From("Configuration.Settings")
                                  ->Where(["Domain" => $Domain])
                        as
                        $Setting
                    ) {
                        yield $Setting["Tag"] => new Setting($Setting["Tag"], $Setting["Value"], $Setting["Type"]);
                    }
                })(),
                $Domain
            )
        );

        return $this->Elements[$Domain] ?? [];

    }

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Settings {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): Settings {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt($Key): Settings {
        return parent::RemoveAt($Key);
    }
    
    /**
     * Saves the values of the Remote Settings to the database.
     *
     * @param bool $Create Flag indicating whether to create a new set of Settings instead of updating existing Settings.
     */
    public function Save(bool $Create = false): void {
        foreach($this as $Settings) {
            $Settings->Save($Create);
        }
    }

}