<?php

namespace vDesk\Struct;

use vDesk\Struct\Text\Chain;

/**
 * Stringwrapper class.
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Text {
    
    use Properties;
    
    /**
     * Represents an empty string.
     */
    public const Empty = "";
    
    /**
     * Represents a whitespace character.
     */
    public const Space = " ";
    
    /**
     * The default characters to remove for trim operations.
     */
    public const Trim = " \t\n\r\0\x0B";
    
    /**
     * The 'NULL'-character.
     */
    public const NULL = "\0x00";
    
    /**
     * The internal string of the Text.
     *
     * @var string|null
     */
    private ?string $String = null;
    
    /**
     * Prevent instantiation.
     */
    private function __construct() { }
    
    /**
     * Concatenates the members of a constructed iterable collection of type String, using the specified separator between each member.
     *
     * @param string|\Stringable                       $Separator The string to use as a separator. $Separator is included in the returned string only if
     *                                                            values has more than one element.
     * @param \vDesk\Struct\Text\Chain|string|iterable $Strings   A collection that contains the strings to concatenate.
     *
     * @return \vDesk\Struct\Text\Chain A string that consists of the members of values delimited by the separator string.
     */
    public static function Join(string|\Stringable $Separator, iterable $Strings): Chain {
        return new Chain(\implode((array)$Separator, $Strings));
    }
    
    /**
     * Indicates whether a specified string is null, empty, or consists only of white-space characters.
     *
     * @param string|\vDesk\Struct\Text|\Stringable $String The string to test.
     *
     * @return bool True if the value $String is null or Text::Empty, or if value consists exclusively of white-space characters;
     *              otherwise, false.
     */
    public static function IsNullOrWhitespace(string|self|\Stringable $String): bool {
        return self::IsNullOrEmpty($String) || \ctype_space($String);
    }
    
    /**
     * Indicates whether the specified string is null or an empty string.
     *
     * @param string|\vDesk\Struct\Text|\Stringable $String The string to test.
     *
     * @return bool True if the value $String is null or an empty string (""); otherwise, false.
     */
    public static function IsNullOrEmpty(string|self|\Stringable $String): bool {
        return $String === null || $String === self::Empty;
    }
    
    /**
     * Indicates whether a specified string consists only of alphabetical characters.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to test.
     *
     * @return bool True if the value $String consists only of alphabetical characters; otherwise, false.
     */
    public static function IsAlphabetic(string|self|\Stringable $String): bool {
        return \ctype_alpha($String);
    }
    
    /**
     * Indicates whether a specified string consists only of numerical characters.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to test.
     *
     * @return bool True if the value $String consists only of decimal digit characters; otherwise, false.
     */
    public static function IsNumeric(string|self|\Stringable $String): bool {
        return \ctype_digit($String);
    }
    
    /**
     * Indicates whether a specified string consists only of aphlanumerical characters.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to test.
     *
     * @return bool True if the value $String consists only of alphabetical and/or numerical characters; otherwise, false.
     */
    public static function IsAlphaNumeric(string|self|\Stringable $String): bool {
        return \ctype_alnum($String);
    }
    
    /**
     * Indicates whether a specified string consists only of printable characters.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to test.
     *
     * @return bool True if the value $String consists only of printable characters; otherwise, false.
     */
    public static function IsPrintable(string|self|\Stringable $String): bool {
        return \ctype_print($String);
    }
    
    /**
     * Indicates whether a specified string consists only of lowercase characters.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to test.
     *
     * @return bool True if the value $String consists only of lowercase characters; otherwise, false.
     */
    public static function IsLowerCase(string|self|\Stringable $String): bool {
        return \ctype_lower($String);
    }
    
    /**
     * Indicates whether a specified string consists only of uppercase characters.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to test.
     *
     * @return bool True if the value $String consists only of uppercase characters; otherwise, false.
     */
    public static function IsUpperCase(string|self|\Stringable $String): bool {
        return \ctype_upper($String);
    }
    
    /**
     * Returns a copy of the specified string converted to lowercase.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to convert to convert to uppercase.
     *
     * @return \vDesk\Struct\Text\Chain The capitalized string.
     */
    public static function ToLower(string|self|\Stringable $String): Chain {
        return new Chain(\strtolower($String));
    }
    
    /**
     * Returns a copy of the specified string converted to uppercase.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to convert to convert to lowercase.
     *
     * @return \vDesk\Struct\Text\Chain The lower-cased string.
     */
    public static function ToUpper(string|self|\Stringable $String): Chain {
        return new Chain(\strtoupper($String));
    }
    
    /**
     * Returns a new string in which all occurrences of a specified string in the specified target string are replaced with another
     * specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String   The target string to change all specified occurrences of.
     * @param string|\Stringable|\vDesk\Struct\Text $OldValue The string to be replaced.
     * @param string|\Stringable|\vDesk\Struct\Text $NewValue The string to replace all occurrences of $OldValue.
     *
     * @return \vDesk\Struct\Text\Chain A string that is equivalent to the current string except that all occurrences of $OldValue are replaced with
     *                $NewValue.
     */
    public static function Replace(string|self|\Stringable $String, string|self|\Stringable $OldValue, string|self|\Stringable $NewValue): Chain {
        return new Chain(\str_replace($OldValue, $NewValue, $String));
    }
    
    /**
     * Returns a new string in which all occurrences of any specified strings in the specified target string are replaced with another
     * specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String    The target string to change all specified occurrences of.
     * @param \vDesk\Struct\Text\Chain[]|string[]   $OldValues The strings to be replaced.
     * @param string|\Stringable|\vDesk\Struct\Text $NewValue  The string to replace all occurrences of $OldValues.
     *
     * @return \vDesk\Struct\Text\Chain A string that is equivalent to the current string except that all occurrences of $OldValues are replaced with
     *                $NewValue.
     */
    public static function ReplaceAny(string|self|\Stringable $String, array $OldValues, string|self|\Stringable $NewValue): Chain {
        return new Chain(\str_replace($OldValues, $NewValue, $String));
    }
    
    /**
     * Checks whether a specified substring exists within the target string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String        The string to scan.
     * @param string|\Stringable|\vDesk\Struct\Text $SearchString  The substring to search.
     * @param bool                                  $CaseSensitive Determines whether the comparison should performed case-sensitive.
     *
     * @return bool True if the specified string contains the specified search-string; otherwise, false.
     */
    public static function Contains(string|self|\Stringable $String, string|self|\Stringable $SearchString, bool $CaseSensitive = true): bool {
        return self::IndexOf($String, $SearchString, $CaseSensitive) >= 0;
    }
    
    /**
     * Returns the zero-based index of the first occurrence of the specified search string in the specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String        The string to search the occurence of $Search.
     * @param string|\Stringable|\vDesk\Struct\Text $Search        The substring to search for.
     * @param bool                                  $CaseSensitive Determines whether the search should performed case-sensitive.
     *
     * @return int The zero-based starting index position of $Search if that string is found, or -1 if it is not.
     */
    public static function IndexOf(string|self|\Stringable $String, string|self|\Stringable $Search, bool $CaseSensitive = true): int {
        return ($Position = $CaseSensitive ? \strpos($String, $Search) : \stripos($String, $Search)) !== false ? $Position : -1;
    }
    
    /**
     * Checks whether any of a set of a substrings exist within the target string. Note: the comparison will be performed case-insensitive.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String           The string to scan.
     * @param \vDesk\Struct\Text\Chain|string       ...$SearchStrings The substrings to search.
     *
     * @return bool True if the specified string contains any of the specified search-strings; otherwise, false.
     */
    public static function ContainsAny(string|self|\Stringable $String, ...$SearchStrings): bool {
        foreach(\is_array($SearchStrings[0]) ? $SearchStrings[0] : $SearchStrings as $SearchString) {
            if(self::IndexOf($String, $SearchString, false) >= 0) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Concatenates two or more strings.
     *
     * @param string ...$Strings
     *
     * @return \vDesk\Struct\Text\Chain The concatenation of the specified strings.
     */
    public static function Concat(string ...$Strings): Chain {
        return self::Join(self::Empty, $Strings);
    }
    
    /**
     * Determines whether the start of a string matches the specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String        The string to search for the presence of the specified search-string.
     * @param string|\Stringable|\vDesk\Struct\Text $Search        The search-string to compare to the substring at the start of the string.
     * @param bool                                  $CaseSensitive Determines whether the comparison should performed case-sensitive.
     *
     * @return bool True if the specified string contains the specified search-string at its start; otherwise, false.
     */
    public static function StartsWith(string|self|\Stringable $String, string|self|\Stringable $Search, bool $CaseSensitive = true): bool {
        return self::IndexOf($String, $Search, $CaseSensitive) === 0;
    }
    
    /**
     * Determines whether the end of a string matches the specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String        The string to search for the presence of the specified search-string.
     * @param string|\Stringable|\vDesk\Struct\Text $Search        The search-string to compare to the substring at the end of the string.
     * @param bool                                  $CaseSensitive Determines whether the comparison should performed case-sensitive.
     *
     * @return bool True if the specified string contains the specified search-string at its end; otherwise, false.
     */
    public static function EndsWith(string|self|\Stringable $String, string|self|\Stringable $Search, bool $CaseSensitive = true): bool {
        return self::LastIndexOf($String, $Search, $CaseSensitive) === (\strlen($String) - \strlen($Search));
    }
    
    /**
     * Returns the zero-based index position of the last occurrence of the specified search string in the specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String        The string to search the occurence of $Search.
     * @param string|\Stringable|\vDesk\Struct\Text $Search        The substring to search for.
     * @param bool                                  $CaseSensitive Determines whether the search should performed case-sensitive.
     *
     * @return int The zero-based starting index position of $Search if that string is found, or -1 if it is not.
     */
    public static function LastIndexOf(string|self|\Stringable $String, string|self|\Stringable $Search, bool $CaseSensitive = true): int {
        return (($Position = $CaseSensitive ? \strrpos($String, $Search) : \strripos($String, $Search)) !== false) ? $Position : -1;
    }
    
    /**
     * Returns the amount of characters of the specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string whose characters will be counted.
     *
     * @return int The amount of characters of the specified string.
     */
    public static function Length(string|self|\Stringable $String): int {
        return \strlen($String);
    }
    
    /**
     * Returns a new string that aligns the characters in the specified string by padding them on both sides with a specified Unicode
     * character, for a specified total length.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to pad.
     * @param int                                   $Amount The number of characters in the resulting string, equal to the number of original characters plus any
     *                                                      additional padding characters.
     * @param string|\Stringable|\vDesk\Struct\Text $Char   A Unicode padding character.
     *
     * @return \vDesk\Struct\Text\Chain A new string that is equivalent to the specified string, padded on both sides with as many $Char characters as needed
     *                to create a length of $Amount.
     */
    public static function Pad(string|self|\Stringable $String, int $Amount, string|self|\Stringable $Char = self::Space): Chain {
        return new Chain(\str_pad($String, $Amount, $Char, \STR_PAD_BOTH));
    }
    
    /**
     * Returns a new string that right-aligns the characters in the specified string by padding them on the left with a specified Unicode
     * character, for a specified total length.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to pad.
     * @param int                                   $Amount The number of characters in the resulting string, equal to the number of original characters plus any
     *                                                      additional padding characters.
     * @param string|\Stringable|\vDesk\Struct\Text $Char   A Unicode padding character.
     *
     * @return \vDesk\Struct\Text\Chain A new string that is equivalent to the specified string, but right-aligned and padded on the left with as many $Char
     *                 characters as needed to create a length of $Amount.
     */
    public static function PadLeft(string|self|\Stringable $String, int $Amount, string|self|\Stringable $Char = self::Space): Chain {
        return new Chain(\str_pad($String, $Amount, $Char, \STR_PAD_LEFT));
    }
    
    /**
     * Returns a new string that left-aligns the characters in the specified string by padding them on the right with a specified Unicode
     * character, for a specified total length.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to pad.
     * @param int                                   $Amount The number of characters in the resulting string, equal to the number of original characters plus any
     *                                                      additional padding characters.
     * @param string|\Stringable|\vDesk\Struct\Text $Char   A Unicode padding character.
     *
     * @return \vDesk\Struct\Text\Chain A new string that is equivalent to the specified string, but left-aligned and padded on the right with as many $Char
     *                 characters as needed to create a length of $Amount.
     */
    public static function PadRight(string|self|\Stringable $String, int $Amount, string|self|\Stringable $Char = self::Space): Chain {
        return new Chain(\str_pad($String, $Amount, $Char, \STR_PAD_RIGHT));
    }
    
    /**
     * Splits a string into substrings that are based on the specified delimiter character.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String    The string to split.
     * @param string|\Stringable|\vDesk\Struct\Text $Delimiter A character that delimits the substrings in the specified string.
     *
     * @return \vDesk\Struct\Text[] An array whose elements contain the substrings from the specified string that are delimited by one or
     *                              more characters in $Delimiter.
     */
    public static function Split(string|self|\Stringable $String, string|self|\Stringable $Delimiter): array {
        return \array_map(static fn(string|self|\Stringable $String): Chain => new Chain($String), \explode($Delimiter, $String));
    }
    
    /**
     * Removes all leading and trailing occurrences of a set of characters specified in a character mask from the specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text      $String The string to trim.
     * @param null|string|\Stringable|\vDesk\Struct\Text $Chars  The characters to trim.
     *
     * @return \vDesk\Struct\Text\Chain The string that remains after all occurrences of the characters in the $Chars parameter are removed from the start
     *                and end of the specified string.
     */
    public static function Trim(string|self|\Stringable $String, string|self|\Stringable $Chars = null): Chain {
        return new Chain($Chars === null ? \trim($String) : \trim($String, $Chars));
    }
    
    /**
     * Removes all leading occurrences of a set of characters specified in a character mask from the the specified string.
     *
     * @param string|\Stringable|\vDesk\Struct\Text      $String The string to trim.
     * @param null|string|\Stringable|\vDesk\Struct\Text $Chars  The characters to trim.
     *
     * @return \vDesk\Struct\Text\Chain The string that remains after all occurrences of the characters in the $Chars parameter are removed from the start of
     *                the specified string.
     */
    public static function TrimStart(string|self|\Stringable $String, string|self|\Stringable $Chars = null): Chain {
        return new Chain($Chars === null ? \ltrim($String) : \ltrim($String, $Chars));
    }
    
    /**
     * Removes all trailing occurrences of a set of characters specified in a character mask from the current String object.
     *
     * @param string|\Stringable|\vDesk\Struct\Text      $String The string to trim.
     * @param null|string|\Stringable|\vDesk\Struct\Text $Chars  The characters to trim.
     *
     * @return \vDesk\Struct\Text\Chain The string that remains after all occurrences of the characters in the $Chars parameter are removed from the end of
     *                the specified string.
     */
    public static function TrimEnd(string|self|\Stringable $String, string|self|\Stringable $Chars = null): Chain {
        return new Chain($Chars === null ? \rtrim($String) : \rtrim($String, $Chars));
    }
    
    /**
     * Repeats the specified string|self|\Stringable $Amount times.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to repeat.
     * @param int                                   $Amount The amount of times to repeat the specified string.
     *
     * @return \vDesk\Struct\Text\Chain The repeated string.
     */
    public static function Repeat(string|self|\Stringable $String, int $Amount): Chain {
        return new Chain(\str_repeat($String, $Amount));
    }
    
    /**
     * Extracts a substring between two specified identifier chars.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String        The string to extract a substring of.
     * @param string|\Stringable|\vDesk\Struct\Text $Start         The starting character identifier of a substring in the specified string.
     * @param string|\Stringable|\vDesk\Struct\Text $End           The delimiting character identifier of a substring in the specified string.
     * @param bool                                  $CaseSensitive Determines whether the identifier chars should performed case-sensitive.
     *
     *
     * @return \vDesk\Struct\Text\Chain A string that is equivalent to the substring between $Start and $End,
     * or Text::Empty if at least one delimiter can't be found in the specified searchstring.
     */
    public static function Extract(string|self|\Stringable $String, string|self|\Stringable $Start, string|self|\Stringable $End, bool $CaseSensitive = true): Chain {
        if(
            ($Start = self::IndexOf($String, $Start[0], $CaseSensitive)) > 0
            && ($End = self::LastIndexOf($String, $End[0], $CaseSensitive)) > 0
        ) {
            // Flip indices if $Start is higher than $End.
            if($Start > $End) {
                $Temp  = $Start;
                $Start = $End;
                $End   = $Temp;
            }
            //Extract string portion.
            return self::Substring($String, $Start, $End - $Start);
        }
        return new Chain(self::Empty);
    }
    
    /**
     * Retrieves a substring from the specified string. The substring starts at a specified character position and has a specified length.
     *
     * @param string|\Stringable|\vDesk\Struct\Text $String The string to retrieve a substring of.
     * @param int                                   $Index  The zero-based starting character position of a substring in the specified string.
     * @param null|int                              $Length The number of characters in the substring.
     *
     * @return \vDesk\Struct\Text\Chain A string that is equivalent to the substring of $Length length that begins at $Index in the specified string,
     *                       or Text::Empty if $Index is equal to the length of the specified string and $Length is zero.
     *                       If $Length is omitted, the substring starting from $Index until the end of the string will be returned.
     */
    public static function Substring(string|self|\Stringable $String, int $Index, ?int $Length = null): Chain {
        $SubString = $Length === null
            ? \substr($String, $Index)
            : \substr($String, $Index, $Length);
        return new Chain(
            $SubString !== false
                ? $SubString
                : self::Empty
        );
    }
    
    /**
     * @return string
     * @ignore
     */
    public function __toString() {
        return $this->String;
    }
    
}

