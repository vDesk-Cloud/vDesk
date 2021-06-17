<?php
declare(strict_types=1);

namespace vDesk\Crash\Test\Case;

use vDesk\Crash\Attribute;
use vDesk\Crash\Random;

/**
 * Attribute for passing predefined or randomized values to Test cases.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Values extends Attribute {

    /**
     * The amount of random values to generate.
     *
     * @var null|int
     */
    public ?int $Random = null;

    /**
     * The values of the Attribute.
     *
     * @var array
     */
    public array $Values = [];

    /**
     * Initializes a new instance of the Crash Attribute class.
     *
     * @param mixed ...$Values Initializes the Values Attribute with the specified values.
     */
    public function __construct(...$Values) {
        $this->Apply($Values);
        if($this->Random !== null) {
            $this->Values = Random::Values($this->Random);
        } else {
            $this->Values = $Values;
        }
    }

    /**
     * @inheritDoc
     */
    public static function FromReflector(\ReflectionAttribute $Reflector): Values {
        return new static(...$Reflector->getArguments());
    }

}