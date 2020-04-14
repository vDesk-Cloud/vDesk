<?php
declare(strict_types=1);

namespace vDesk\Configuration\Settings;

use vDesk\Configuration\Settings\Local\Settings;
use vDesk\IO\File;
use vDesk\IO\Path;
use vDesk\Struct\Collections\Typed\Dictionary;

/**
 * Respresents a Dictionary of {@link \vDesk\Configuration\Settings\Remote\Setting}s.
 *
 * @property-read string $Domain The configuration domain.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Local extends Dictionary {

    /**
     * The Type of the value of the Local.
     */
    public const Type = Settings::class;

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
     * @inheritdoc
     */
    public function offsetGet($Domain) {
        if(isset($this->Elements[$Domain])) {
            return $this->Elements[$Domain];
        }
        // Check if the config file exists.
        if(File::Exists($Path = \Server . Path::Separator . "Settings" . Path::Separator . $Domain . ".php")) {
            $this->Add($Domain, new Settings(include $Path, $Domain));
        } else {
            $this->Add($Domain, new Settings([], $Domain));
        }
        return $this->Elements[$Domain];
    }

    /**
     * Serializes the values of the local Settings to files.
     *
     * @param string $Path The path where the Settings files will be saved to.
     */
    public function Save(string $Path = \Server . Path::Separator . "Settings"): void {
        foreach($this as $Settings) {
            $Settings->Save($Path);
        }
    }

}
