<?php
declare(strict_types=1);

use vDesk\Configuration\Settings;
use vDesk\Pages\IPackage;
use vDesk\Pages\Request;

/**
 * Concatenates multiple parts to an URL, relative to the specified base-URL (Config::URL::Base).
 *
 * @param string ...$Parts The parts of the URL to build.
 *
 * @return string An URL relative to the base-URL.
 */
function Script(string ...$Parts): string {
    $Chunks = [];
    
    foreach($Parts as $Part) {
        foreach(\explode("/", $Part) as $Chunk) {
            $Chunks[] = $Chunk;
        }
    }
    
    return "http"
        . (isset($_SERVER["HTTPS"]) ? "s" : "")
        . "://"
        . (Settings::$Local["Pages"]["Host"] ?? Request::$Host . \str_replace("Pages.php?/", "", \str_replace(Request::$QueryString, "/", Request::$URI)))
        . IPackage::Scripts
        . "/"
        . \implode("/", $Chunks)
        . ".js";
}