<?php
declare(strict_types=1);

namespace vDesk\Documentation;

/**
 * Utility class for creation of colored html code blocks.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Code {
    
    /**
     * Code block for recommended practices.
     */
    public const PHP = "<span class=\"Keyword\">&lt;?php</span>";
    
    /**
     * Code block for semicolon code delimiter.
     */
    public const Delimiter = "<span class=\"Keyword\">;</span>";
    
    /**
     * Code block for the "new" keyword.
     */
    public const New       = "<span class=\"Keyword\">new</span>";
    
    /**
     * Code block for the "throw" keyword.
     */
    public const Throw     = "<span class=\"Keyword\">throw</span>";
    
    /**
     * Code block for the "return" keyword.
     */
    public const Return    = "<span class=\"Keyword\">return</span>";
    /**
     * Code block for the instance based scope resolution operator.
     */
    public const This = "<span class=\"Keyword\">this</span>";
    
    /**
     * Code block for the instance based scope resolution operator.
     */
    public const Scope = "<span class=\"Keyword\">-></span>";
    
    /**
     * Code block for the "self" static class resolution operator.
     */
    public const Self   = "<span class=\"Keyword\">self</span>";
    
    /**
     * Code block for the static class resolution operator.
     */
    public const Static = "<span class=\"Keyword\">static</span>";
    
    /**
     * Code block for the parent scope resolution operator.
     */
    public const Parent = "<span class=\"Keyword\">parent</span>";
    
    /**
     * Code block for the "delete" keyword.
     */
    public const Delete = "<span class=\"Keyword\">delete</span>";
    
    /**
     * Code block for the "null" keyword.
     */
    public const Null = "<span class=\"Keyword\">null</span>";
    
    /**
     * Code block for the "true" keyword.
     */
    public const True = "<span class=\"Keyword\">true</span>";
    
    /**
     * Code block for the "false" keyword.
     */
    public const False      = "<span class=\"Keyword\">false</span>";
    
    /**
     * Code block for the "if" keyword.
     */
    public const If         = "<span class=\"Keyword\">if</span>";
    
    /**
     * Code block for the "instanceof" keyword.
     */
    public const InstanceOf = "<span class=\"Keyword\">instanceof</span>";
    
    /**
     * Code block for the "typeof" keyword.
     */
    public const TypeOf    = "<span class=\"Keyword\">typeof</span>";
    
    /**
     * Code block for the "else" keyword.
     */
    public const Else      = "<span class=\"Keyword\">else</span>";
    
    /**
     * Code block for the "switch" keyword.
     */
    public const Switch    = "<span class=\"Keyword\">switch</span>";
    
    /**
     * Code block for the "case" keyword.
     */
    public const Case      = "<span class=\"Keyword\">case</span>";
    
    /**
     * Code block for the "break" keyword.
     */
    public const Break     = "<span class=\"Keyword\">break</span>";
    
    /**
     * Code block for the "continue" keyword.
     */
    public const Continue  = "<span class=\"Keyword\">continue</span>";
    
    /**
     * Code block for the "for" keyword.
     */
    public const For       = "<span class=\"Keyword\">for</span>";
    
    /**
     * Code block for the "while" keyword.
     */
    public const While     = "<span class=\"Keyword\">while</span>";
    
    /**
     * Code block for the "foreach" keyword.
     */
    public const ForEach   = "<span class=\"Keyword\">foreach</span>";
    
    /**
     * Code block for the "do" keyword.
     */
    public const Do        = "<span class=\"Keyword\">do</span>";
    
    /**
     * Code block for the "function" keyword.
     */
    public const Function  = "<span class=\"Keyword\">function</span>";
    
    /**
     * Code block for the "public" modifier.
     */
    public const Public    = "<span class=\"Keyword\">public</span>";
    
    /**
     * Code block for the "protected" modifier.
     */
    public const Protected = "<span class=\"Keyword\">protected</span>";
    
    /**
     * Code block for the "private" modifier.
     */
    public const Private   = "<span class=\"Keyword\">private</span>";
    
    /**
     * Code block for the "const" keyword.
     */
    public const Constant = "<span class=\"Keyword\">const</span>";
    
    /**
     * Code block for the "let" keyword.
     */
    public const Let = "<span class=\"Keyword\">let</span>";
    
    /**
     * Code block for the "var" keyword.
     */
    public const Var = "<span class=\"Keyword\">var</span>";
    
    /**
     * Code block for the "async" keyword.
     */
    public const Async = "<span class=\"Keyword\">async</span>";
    /**
     * Code block for the "await" keyword.
     */
    public const Await    = "<span class=\"Keyword\">await</span>";
    
    /**
     * Code block for the "abstract" keyword.
     */
    public const Abstract = "<span class=\"Keyword\">abstract</span>";
    
    /**
     * Code block for the "class" keyword.
     */
    public const ClassDeclaration = "<span class=\"Keyword\">class</span>";
    
    /**
     * Code block for the "trait" keyword.
     */
    public const Trait            = "<span class=\"Keyword\">trait</span>";
    
    /**
     * Code block for the "interface" keyword.
     */
    public const Interface        = "<span class=\"Keyword\">interface</span>";
    
    /**
     * Code block for the "namespace" keyword.
     */
    public const Namespace        = "<span class=\"Keyword\">namespace</span>";
    
    /**
     * Code block for the "use" keyword.
     */
    public const Use              = "<span class=\"Keyword\">use</span>";
    
    /**
     * Code block for the "implements" keyword.
     */
    public const Implements       = "<span class=\"Keyword\">implements</span>";
    
    /**
     * Code block for the "extends" keyword.
     */
    public const Extends          = "<span class=\"Keyword\">extends</span>";
    
    /**
     * Code block for the "declare" keyword.
     */
    public const Declare          = "<span class=\"Keyword\">declare</span>";
    
    /**
     * Code block for the "void" keyword.
     */
    public const Void = "<span class=\"Keyword\">void</span>";
    
    /**
     * Creates a DOM-String containing the specified text encapsulated in a HTMLSpanElement with the specified CSS-class.
     *
     * @param string $Class The CSS-class to use to create the code block.
     * @param string $Text  The text of the code block.
     *
     * @return string A DOM-String containing the specified text encapsulated in a HTMLSpanElement with the specified CSS-class.
     */
    public static function Block(string $Class, string $Text): string {
        return "<span class=\"$Class\">{$Text}</span>";
    }
    
    /**
     * Creates a code block with the called method name als the CSS-class.
     *
     * @param $Name string The name of the called method.
     * @param $Text string The text to use.
     *
     * @return string A DOM-String containing a code block with the called method name as the CSS-class.
     */
    public static function __callStatic($Name, $Text) {
        return static::Block($Name, ...$Text);
    }
    
    /**
     * Creates a constant code block with a specified text.
     *
     * @param string $Text The text of the code block.
     *
     * @return string A code block representing a constant value.
     */
    public static function Const(string $Text): string {
        return static::Block("Const", $Text);
    }
}