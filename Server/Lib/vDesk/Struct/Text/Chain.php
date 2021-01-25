<?php

namespace vDesk\Struct\Text;

use vDesk\Struct\Properties;
use vDesk\Struct\Text;

/**
 * Stringwrapper class.
 *
 * @property-read int Length Gets the length of the String.
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Chain implements \IteratorAggregate {
    
    use Properties;
    
    /**
     * The internal string of the Text\Chain.
     *
     * @var string|null
     */
    private ?string $String;
    
    /**
     * Initializes a new instance of the Text\Chain class.
     *
     * @param string $String Initializes the Text\Chain with the specified string.
     */
    public function __construct(?string $String = Text::Empty) {
        $this->String = $String;
        $this->AddProperty("Length",
            [
                \Get => fn(): int => \strlen($this->String)
            ]);
    }
    
    /**
     * Concatenates the members of a constructed iterable collection of type String, using the specified separator between each member.
     *
     * @param string          $Separator The string to use as a separator. $Separator is included in the returned string only if values has
     *                                   more than one element.
     * @param string|iterable $Strings   A collection of strings to concatenate.
     *
     * @return \vDesk\Struct\Text\Chain A string that consists of the members of values delimited by the separator string.
     */
    public function Join(string $Separator, iterable $Strings): Chain {
        return new static(\implode($Separator, [$this->String, ...$Strings]));
    }
    
    /**
     * Indicates whether the Text\Chain is null, empty, or consists only of white-space characters.
     *
     * @return bool True if the Text\Chain is null or Text::Empty, or if value consists exclusively of white-space characters; otherwise, false.
     */
    public function IsNullOrWhitespace(): bool {
        return $this->IsNullOrEmpty() || \ctype_space($this->String);
    }
    
    /**
     * Indicates whether the specified string is null or an empty string.
     *
     * @return bool True if the Text\Chain is null or an empty string (""); otherwise, false.
     */
    public function IsNullOrEmpty(): bool {
        return $this->String === null || $this->String === Text::Empty;
    }
    
    /**
     * Indicates whether the Text\Chain consists only of alphabetical characters.
     *
     * @return bool True if the Text\Chain consists only of alphabetical characters; otherwise, false.
     */
    public function IsAlphabetic(): bool {
        return \ctype_alpha($this->String);
    }
    
    /**
     * Indicates whether the Text\Chain consists only of numerical characters.
     *
     * @return bool True if the Text\Chain consists only of decimal digit characters; otherwise, false.
     */
    public function IsNumeric(): bool {
        return \ctype_digit($this->String);
    }
    
    /**
     * Indicates whether the Text\Chain consists only of aphlanumerical characters.
     *
     * @return bool True if the Text\Chain consists only of alphabetical and/or numerical characters; otherwise, false.
     */
    public function IsAlphaNumeric(): bool {
        return \ctype_alnum($this->String);
    }
    
    /**
     * Indicates whether the Text\Chaing consists only of printable characters.
     *
     * @return bool True if the Text\Chain consists only of printable characters; otherwise, false.
     */
    public function IsPrintable(): bool {
        return \ctype_print($this->String);
    }
    
    /**
     * Indicates whether the Text\Chain consists only of lowercase characters.
     *
     * @return bool True if the Text\Chain consists only of lowercase characters; otherwise, false.
     */
    public function IsLowerCase(): bool {
        return \ctype_lower($this->String);
    }
    
    /**
     * Indicates whether the Text\Chain consists only of uppercase characters.
     *
     * @return bool True if the Text\Chain consists only of uppercase characters; otherwise, false.
     */
    public function IsUpperCase(): bool {
        return \ctype_upper($this->String);
    }
    
    /**
     * Converts the characters of the Text\Chain to lowercase.
     *
     * @return \vDesk\Struct\Text\Chain The lower-cased string.
     */
    public function ToLower(): Chain {
        $this->String = \strtolower($this->String);
        return $this;
    }
    
    /**
     * Converts the characters of the Text\Chain to uppercase.
     *
     * @return \vDesk\Struct\Text\Chain The capitalized string.
     */
    public function ToUpper(): Chain {
        $this->String = \strtoupper($this->String);
        return $this;
    }
    
    /**
     * Replaces all occurrences of the Text\Chains of the Text\Chain with another specified string.
     *
     * @param string $OldValue The string to be replaced.
     * @param string $NewValue The string to replace all occurrences of $OldValue.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function Replace(string $OldValue, string $NewValue): Chain {
        $this->String = \str_replace($OldValue, $NewValue, $this->String);
        return $this;
    }
    
    /**
     * Replaces all occurrences of any specified strings of the Text\Chain with another specified string.
     *
     * @param string[] $OldValues The strings to be replaced.
     * @param string   $NewValue  The string to replace all occurrences of $OldValues.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function ReplaceAny(array $OldValues, string $NewValue): Chain {
        $this->String = \str_replace($OldValues, $NewValue, $this->String);
        return $this;
    }
    
    /**
     * Checks whether a specified substring exists within the target string.
     *
     * @param string $SearchString  The substring to search.
     * @param bool   $CaseSensitive Determines whether the comparison should performed case-sensitive.
     *
     * @return bool True if the specified string contains the specified search-string; otherwise, false.
     */
    public function Contains(string $SearchString, bool $CaseSensitive = true): bool {
        return $this->IndexOf($SearchString, $CaseSensitive) >= 0;
    }
    
    /**
     * Returns the zero-based index of the first occurrence of the specified search string in the specified string.
     *
     * @param string $Search        The substring to search for.
     * @param bool   $CaseSensitive Determines whether the search should performed case-sensitive.
     *
     * @return int The zero-based starting index position of $Search if that string is found, or -1 if it is not.
     */
    public function IndexOf(string $Search, bool $CaseSensitive = true): int {
        return ($Position = $CaseSensitive ? \strpos($this->String, $Search) : \stripos($this->String, $Search)) !== false ? $Position : -1;
    }
    
    /**
     * Checks whether any of a set of a substrings exist within the target string. Note: the comparison will be performed case-insensitive.
     *
     * @param string|array $SearchStrings The substrings to search.
     *
     * @return bool True if the specified string contains any of the specified search-strings; otherwise, false.
     */
    public function ContainsAny(...$SearchStrings): bool {
        foreach(\is_array($SearchStrings[0]) ? $SearchStrings[0] : $SearchStrings as $SearchString) {
            if($this->IndexOf($SearchString, false) >= 0) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Concatenates two or more strings.
     *
     * @param string ...$Strings The strings to concatenate.
     *
     * @return \vDesk\Struct\Text\Chain The concatenation of the specified strings.
     */
    public function Concat(string ...$Strings): Chain {
        return $this->Join(Text::Empty, $Strings);
    }
    
    /**
     * Determines whether the start of a string matches the specified string.
     *
     * @param string $Search        The search-string to compare to the substring at the start of the string.
     * @param bool   $CaseSensitive Determines whether the comparison should performed case-sensitive.
     *
     * @return bool True if the specified string contains the specified search-string at its start; otherwise, false.
     */
    public function StartsWith(string $Search, bool $CaseSensitive = true): bool {
        return $this->IndexOf($Search, $CaseSensitive) === 0;
    }
    
    /**
     * Determines whether the end of a string matches the specified string.
     *
     * @param string $Search        The search-string to compare to the substring at the end of the string.
     * @param bool   $CaseSensitive Determines whether the comparison should performed case-sensitive.
     *
     * @return bool True if the specified string contains the specified search-string at its end; otherwise, false.
     */
    public function EndsWith(string $Search, bool $CaseSensitive = true): bool {
        return $this->LastIndexOf($Search, $CaseSensitive) === (\strlen($this->String) - \strlen($Search));
    }
    
    /**
     * Returns the zero-based index position of the last occurrence of the specified search string in the specified string.
     *
     * @param string $Search        The substring to search for.
     * @param bool   $CaseSensitive Determines whether the search should performed case-sensitive.
     *
     * @return int The zero-based starting index position of $Search if that string is found, or -1 if it is not.
     */
    public function LastIndexOf(string $Search, bool $CaseSensitive = true): int {
        return ($Position = $CaseSensitive ? \strrpos($this->String, $Search) : \strripos($this->String, $Search)) !== false ? $Position : -1;
    }
    
    /**
     * Returns the amount of characters of the specified string.
     *
     * @return int The amount of characters of the specified string.
     */
    public function Length(): int {
        return \strlen($this->String);
    }
    
    /**
     * Aligns the characters of the Text\Chain by padding them on both sides with a specified Unicode character, for a specified total length.
     *
     * @param int    $Amount The number of characters in the resulting string, equal to the number of original characters plus any
     *                       additional padding characters.
     * @param string $Char   A Unicode padding character.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function Pad(int $Amount, string $Char = Text::Space): Chain {
        $this->String = \str_pad($this->String, $Amount, $Char, \STR_PAD_BOTH);
        return $this;
    }
    
    /**
     * Right-aligns the characters in the Text\Chain string by padding them on the left with a specified Unicode
     * character, for a specified total length.
     *
     * @param int    $Amount The number of characters in the resulting string, equal to the number of original characters plus any
     *                       additional padding characters.
     * @param string $Char   A Unicode padding character.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function PadLeft(int $Amount, string $Char = Text::Space): Chain {
        $this->String = \str_pad($this->String, $Amount, $Char, \STR_PAD_LEFT);
        return $this;
    }
    
    /**
     * Returns a new string that left-aligns the characters in the specified string by padding them on the right with a specified Unicode
     * character, for a specified total length.
     *
     * @param int    $Amount The number of characters in the resulting string, equal to the number of original characters plus any
     *                       additional padding characters.
     * @param string $Char   A Unicode padding character.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function PadRight(int $Amount, string $Char = Text::Space): Chain {
        $this->String = \str_pad($this->String, $Amount, $Char, \STR_PAD_RIGHT);
        return $this;
    }
    
    /**
     * Splits a string into substrings that are based on the specified delimiter character.
     *
     * @param string $Delimiter A character that delimits the substrings in the specified string.
     *
     * @return \vDesk\Struct\Text\Chain[] An array whose elements contain the substrings from the specified string that are delimited by one or
     *                              more characters in $Delimiter.
     */
    public function Split(string $Delimiter): array {
        return \array_map(static fn(): Chain => new static($this->String), \explode($Delimiter, $this->String));
    }
    
    /**
     * Removes all leading and trailing occurrences of a set of characters specified in a character mask from the specified string.
     *
     * @param string $Chars The characters to trim.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function Trim(string $Chars = null): Chain {
        $this->String = $Chars === null ? \trim($this->String) : \trim($this->String, $Chars);
        return $this;
    }
    
    /**
     * Removes all leading occurrences of a set of characters specified in a character mask from the the specified string.
     *
     * @param string $Chars The characters to trim.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function TrimStart(string $Chars = null): Chain {
        $this->String = $Chars === null ? \ltrim($this->String) : \ltrim($this->String, $Chars);
        return $this;
    }
    
    /**
     * Removes all trailing occurrences of a set of characters specified in a character mask from the current String object.
     *
     * @param string $Chars The characters to trim.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function TrimEnd(string $Chars = null): Chain {
        $this->String = $Chars === null ? \rtrim($this->String) : \rtrim($this->String, $Chars);
        return $this;
    }
    
    /**
     * Repeats the specified string $Amount times.
     *
     * @param int $Amount The amount of times to repeat the specified string.
     *
     * @return \vDesk\Struct\Text\Chain The current instance of the Text\Chain class for further chaining operations.
     */
    public function Repeat(int $Amount): Chain {
        $this->String = \str_repeat($this->String, $Amount);
        return $this;
    }
    
    /**
     * Extracts a substring between two specified identifier chars.
     *
     * @param string $Start         The starting character identifier of a substring in the specified string.
     * @param string $End           The delimiting character identifier of a substring in the specified string.
     * @param bool   $CaseSensitive Determines whether the identifier chars should performed case-sensitive.
     *
     *
     * @return \vDesk\Struct\Text\Chain|string A Text\Chain that is equivalent to the substring between $Start and $End,
     * or Text::Empty if at least one delimiter can't be found in the specified searchstring.
     */
    public function Extract(string $Start, string $End, bool $CaseSensitive = true): Chain {
        if(
            ($Start = $this->IndexOf($Start[0], $CaseSensitive)) > 0
            && ($End = $this->LastIndexOf($End[0], $CaseSensitive)) > 0
        ) {
            // Flip indices if $Start is higher than $End.
            if($Start > $End) {
                $Temp  = $Start;
                $Start = $End;
                $End   = $Temp;
            }
            
            //Extract string portion.
            return $this->Substring($Start, $End - $Start);
            
        }
        return Text::Empty;
    }
    
    /**
     * Retrieves a substring from the specified string. The substring starts at a specified character position and has a specified length.
     *
     * @param int $Index  The zero-based starting character position of a substring in the specified string.
     * @param int $Length The number of characters in the substring.
     *
     * @return \vDesk\Struct\Text\Chain|string A Text\Chain that is equivalent to the substring of $Length length that begins at $Index in the specified string,
     *                       or Text::Empty if $Index is equal to the length of the specified string and $Length is zero.
     *                       If $Length is omitted, the substring starting from $Index until the end of the string will be returned.
     */
    public function Substring(int $Index, ?int $Length = null): Chain {
        $SubString = $Length === null
            ? \substr($this->String, $Index)
            : \substr($this->String, $Index, $Length);
        return new Chain(
            $SubString !== false
                ? $SubString
                : Text::Empty
        );
    }
    
    /**
     * @return string
     * @ignore
     */
    public function __toString() {
        return $this->String;
    }
    
    /**
     * Gets a Generator that iterates over the characters of the string-value of the Text.
     *
     * @return \Generator A Generator that yields the characters of the Text.
     */
    public function getIterator(): \Generator {
        for($Index = 0, $Length = $this->Length; $Index < $Length; $Index++) {
            yield $this->String[$Index];
        }
    }
    
}

