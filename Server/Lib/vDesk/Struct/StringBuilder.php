<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Represents a mutable string of characters.
 *
 * @property int Length Gets or sets the length of the value of the StringBuilder.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
final class StringBuilder {

    use Properties;

    /**
     * The character storage of the StringBuilder.
     *
     * @var string[]
     */
    private array $Storage = [];

    /**
     * Initializes a new instance of the StringBuilder class.
     */
    public function __construct() {

        $this->AddProperty("Length", [
            \Get => function(): int {
                $iLength = 0;
                foreach($this->Storage as $sString) {
                    $iLength += \strlen($sString);
                }
                return $iLength;
            },
            \Set => function(int $Size) {
                if($Size === 0) {
                    $this->Clear();
                } else {
                    $sContent = $this->ToString();
                    \array_splice($this->Storage, 0);
                    $this->Storage[] = \substr($sContent, 0, $Size);
                }
            }
        ]);

    }

    /**
     * Appends a copy of the specified string to this instance.
     *
     * @param string $Value The string to append.
     *
     * @return StringBuilder A reference to this instance after the append operation has completed.
     */
    public function Append(string $Value): self {
        $this->Storage[] = $Value;
        return $this;
    }

    /**
     * Converts the value of this instance to a String.
     *
     * @return string A string whose value is the same as this instance.
     */
    public function ToString(): string {
        return \implode("", $this->Storage);
    }

    /**
     * Removes all characters from the current StringBuilder instance.
     * Clear is a convenience method that is equivalent to setting the Length property of the current instance to 0 (zero).
     */
    public function Clear(): void {
        \array_splice($this->Storage, 0);
    }

    /**
     *
     *
     * @return string
     */
    public function __toString(): string {
        return $this->ToString();
    }

}
