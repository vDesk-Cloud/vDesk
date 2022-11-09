<?php

namespace vDesk\Pages;

use vDesk\Configuration\Settings;

/**
 * Utility class for creating URLs.
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class URL {

    /**
     * Concatenates multiple parts to an URL, relative to the specified base-URL (Config::URL::Base).
     *
     * @param string ...$Parts The parts of the URL to build.
     *
     * @return string An URL relative to the base-URL.
     */
    public static function Page(string ...$Parts): string {
        $Chunks = [];

        foreach($Parts as $Part) {
            foreach(\explode("/", $Part) as $Chunk) {
                $Chunks[] = $Chunk;
            }
        }

        return "http"
            . (isset($_SERVER["HTTPS"]) ? "s" : "")
            . "://"
            . (Settings::$Local["Pages"]["Host"] ?? Request::$Host . \str_replace(Request::$QueryString, "/", Request::$URI))
            . \implode("/", $Chunks);
    }

    /**
     * Concatenates multiple parts to an URL, relative to the specified base-URL (Config::URL::Base).
     *
     * @param string ...$Parts The parts of the URL to build.
     *
     * @return string An URL relative to the base-URL.
     */
    public static function Stylesheet(string ...$Parts): string {
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
            . IPackage::Stylesheets
            . "/"
            . \implode("/", $Chunks)
            . ".css";
    }

    /**
     * Concatenates multiple parts to an URL, relative to the specified base-URL (Config::URL::Base).
     *
     * @param string ...$Parts The parts of the URL to build.
     *
     * @return string An URL relative to the base-URL.
     */
    public static function Script(string ...$Parts): string {
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

    /**
     * Concatenates multiple parts to an URL pointing to an image file, relative to the specified base-URL (Config::URL::Base).
     *
     * @param string ...$Parts The parts of the URL to build.
     *
     * @return string An URL relative to the base-URL.
     */
    public static function Image(string ...$Parts): string {
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
            . IPackage::Images
            . "/"
            . \implode("/", $Chunks);
    }
}