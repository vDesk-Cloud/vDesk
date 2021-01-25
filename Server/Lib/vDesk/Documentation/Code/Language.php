<?php
declare(strict_types=1);

namespace vDesk\Documentation\Code;

/**
 * Enumeration of predefined code blocks for programming languages.
 *
 * @package vDesk\Documentation\Code
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Language {
    
    /**
     * Code block for PHP source code.
     */
    public const PHP = "<h4 class='Language' title='PHP source code'><span class='PHP'>php</span></h4>";
    
    /**
     * Code block for Javascript source code.
     */
    public const JS = "<h4 class='Language JS' title='Javascript source code'><span class='JS'>JS</span></h4>";
    
    /**
     * Code block for SQL source code.
     */
    public const SQL = "<h4 class='Language SQL' title='SQL source code'>SQL</h4>";
    
}