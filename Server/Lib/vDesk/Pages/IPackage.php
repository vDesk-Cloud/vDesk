<?php
declare(strict_types=1);

namespace vDesk\Pages;


/**
 * Interface for Packages that provide websites.
 *
 * @package vDesk\Locale
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IPackage {
    
    /**
     * The Pages folder of the Package.
     */
    public const Pages = "Pages";
    
    /**
     * The templates folder of the Package.
     */
    public const Templates = "Templates";
    
    /**
     * The functions folder of the Package.
     */
    public const Functions = "Functions";
    
    /**
     * The scripts folder of the Package.
     */
    public const Scripts = "Scripts";
    
    /**
     * The stylesheets folder of the Package.
     */
    public const Stylesheets = "Stylesheets";
    
    /**
     * The image folder of the Package.
     */
    public const Images = "Images";
    
}