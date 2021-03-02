<?php
declare(strict_types=1);

namespace Pages\Reflect;


use Pages\Reflect;

class Link extends Reflect {

    /**
     *
     * @param string $Name
     * @param string $Anchor
     * @return string
     */
    public static function Class(string $Name, string $Anchor = ""): string {
        return "./Class." . str_replace("\\", ".", $Name) . ".html" . ($Anchor !== "" ? "#" . $Anchor : $Anchor);
    }

    /**
     *
     * @param string $Name
     * @param string $Anchor
     * @return string
     */
    public static function Interface(string $Name, string $Anchor = ""): string {
        return "./Interface." . str_replace("\\", ".", $Name) . ".html" . ($Anchor !== "" ? "#" . $Anchor : $Anchor);
    }

    /**
     *
     * @param string $Name
     * @param string $Anchor
     * @return string
     */
    public static function Trait(string $Name, string $Anchor = ""): string {
        return "./Trait." . str_replace("\\", ".", $Name) . ".html" . ($Anchor !== "" ? "#" . $Anchor : $Anchor);
    }

    /**
     *
     * @param string $Name
     * @param string $Anchor
     * @return string
     */
    public static function Namespace(string $Name, string $Anchor = ""): string {
        return "./Namespace." . str_replace("\\", ".", $Name) . ".html" . ($Anchor !== "" ? "#" . $Anchor : $Anchor);
    }

    /**
     *
     * @param string $Name
     * @param string $Anchor
     * @param bool $RawName
     * @return string
     */
    public static function ClassTag(string $Name, string $Anchor = "", bool $RawName = false): string {
        if($RawName) {
            return "<a href=\"" . self::Class($Name, $Anchor) . "\">" . (($Offset = strripos($Name, "\\")) !== false ? substr($Name, $Offset + 1) : $Name) . "</a>";
        }
        return "<a href=\"" . self::Class($Name, $Anchor) . "\">{$Name}</a>";
    }

    /**
     *
     * @param string $Name
     * @param string $Anchor
     * @param bool $RawName
     * @return string
     */
    public static function InterfaceTag(string $Name, string $Anchor = "", bool $RawName = false): string {
        if($RawName) {
            return "<a href=\"" . self::Interface($Name, $Anchor) . "\">" . (($Offset = strripos($Name, "\\")) !== false ? substr($Name, $Offset + 1) : $Name) . "</a>";
        }
        return "<a href=\"" . self::Interface($Name, $Anchor) . "\">{$Name}</a>";
    }

    /**
     *
     * @param string $Name
     * @param string $Anchor
     * @param bool $RawName
     * @return string
     */
    public static function TraitTag(string $Name, string $Anchor = "", bool $RawName = false): string {
        if($RawName) {
            return "<a href=\"" . self::Trait($Name, $Anchor) . "\">" . (($Offset = strripos($Name, "\\")) !== false ? substr($Name, $Offset + 1) : $Name) . "</a>";
        }
        return "<a href=\"" . self::Trait($Name, $Anchor) . "\">{$Name}</a>";
    }

    /**
     *
     * @param string $Name
     * @param string $Anchor
     * @return string
     */
    public static function NamespaceTag(string $Name, string $Anchor = ""): string {
        return "<a href=\"" . self::Namespace($Name, $Anchor) . "\">{$Name}</a>";
    }

    /**
     *
     * @param string $Name
     * @return string
     */
    public static function Anchor(string $Name): string {
        return "#$Name";
    }

}