<?php
declare(strict_types=1);

namespace vDesk\Configuration\Settings\Local;

use vDesk\IO\File;
use vDesk\IO\FileNotFoundException;
use vDesk\IO\FileStream;
use vDesk\IO\Path;
use vDesk\IO\Stream\Mode;
use vDesk\Struct\Collections\Dictionary;
use vDesk\Struct\Type;

/**
 * Class that represents a Dictionary of local configuration values.
 *
 * @package vDesk\Configuration
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Settings extends Dictionary {
    
    /**
     * The domain of the Settings.
     *
     * @var string
     */
    private string $Domain;
    
    /**
     * Initializes a new instance of the Settings class.
     *
     * @param iterable|null $Settings Initializes the Settings with the specified Dictionary of local configuration settings.
     * @param string        $Domain   Initializes the Settings with the specified domain.
     */
    public function __construct(protected ?iterable $Settings = [], string $Domain = "") {
        parent::__construct($Settings);
        $this->Domain = $Domain;
        $this->AddProperty("Domain", [\Get => fn(): string => $this->Domain]);
    }
    
    /**
     * Factory method that loads the configuration values of a specified domain.
     *
     * @param string $Domain The domain to load.
     *
     * @return \vDesk\Configuration\Settings\Local\Settings A Dictionary containing the key-value-pairs of the specified configuration
     *                                                      domain.
     * @throws \vDesk\IO\FileNotFoundException Thrown if the config file of the requested domain doesn't exist.
     *
     */
    public static function FromDomain(string $Domain): Settings {
        if(!File::Exists($Path = \Server . Path::Separator . "Settings" . Path::Separator . "{$Domain}.php")) {
            throw new FileNotFoundException("Settings file with domain name '$Domain' doesn't exist.");
        }
        return new static(include $Path, $Domain);
    }
    
    /**
     * Serializes the values of the Settings to the configuration file.
     *
     * @param string $Path The path where the Settings file will be saved to.
     */
    public function Save(string $Path = \Server . Path::Separator . "Settings"): void {
        $File = new FileStream($Path . Path::Separator . "{$this->Domain}.php", Mode::Truncate);
        $File->Write("<?php" . \PHP_EOL);
        $File->Write("return [" . \PHP_EOL);
        $Values = [];
        foreach($this as $Key => $Value) {
            $Values[] = "    \"$Key\" => " . self::Convert($Value);
        }
        $File->Write(\implode("," . \PHP_EOL, $Values) . \PHP_EOL);
        $File->Write("];" . \PHP_EOL);
        $File->Close();
    }
    
    /**
     * Converts a specified value into its PHP code representation.
     *
     * @param mixed $Value The value to convert.
     *
     * @return string
     */
    private static function Convert(mixed $Value): string {
        if($Value === null) {
            return "null";
        }
        if($Value instanceof \DateTime) {
            return "new \DateTime(\"{$Value->format(\DateTime::ATOM)}\")";
        }
        switch(Type::Of($Value)) {
            case Type::String:
                return "\"" . \addslashes($Value) . "\"";
            case Type::Array:
                $Values = [];
                foreach($Value as $Key => $Item) {
                    if(Type::Of($Key) === Type::String) {
                        $Values[] = '"' . $Key . '" => ' . static::Convert($Item);
                    } else {
                        $Values[] = static::Convert($Item);
                    }
                }
                return "[" . \PHP_EOL . "    " . \implode(", " . \PHP_EOL . "    ", $Values) . \PHP_EOL . "]";
            case Type::Bool:
            case Type::Boolean:
                return $Value ? "true" : "false";
            default:
                return (string)$Value;
        }
    }
    
    /**
     * @inheritDoc
     */
    public function offsetGet($Key) {
        return $this->Elements[$Key] ?? null;
    }
    
}