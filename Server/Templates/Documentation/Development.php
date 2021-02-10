<?php

use vDesk\Documentation\Code;
use vDesk\Documentation\Code\Conventions;
use vDesk\Pages\Functions;

?>
<h2>Development</h2>
<p>
    This document describes the general coding conventions of vDesk's source code.<br>
    Before submitting a pull request, consider reading the following specifications.
</p>
<h3>Overview</h3>
<ul class="Topics">
    <li>
        <a href="#CodingStyle">Coding style</a>
        <ul class="Topics">
            <li><a href="#GeneralRecommendations">General recommendations</a></li>
            <li><a href="#NamingConventions">Naming conventions</a></li>
            <li><a href="#TypeCompliance">Type compliance</a></li>
            <li><a href="#CodeBlocks">Code blocks</a></li>
            <li><a href="#VariablesFieldsConstants">Variables, Fields and Constants</a></li>
            <li><a href="#FunctionsMethods">Functions/Methods</a></li>
            <li><a href="#Parameters">Parameters</a></li>
            <li><a href="#Classes">Classes</a></li>
            <li><a href="#Interfaces">Interfaces</a></li>
            <li><a href="#Traits">Traits</a></li>
            <li><a href="#Namespaces">Namespaces</a></li>
            <li><a href="#ErrorHandling">Error handling</a></li>
            <li><a href="#Properties">Properties</a></li>
            <li><a href="#Iteration">Iteration</a></li>
            <li><a href="#Arrays">Arrays</a></li>
            <li><a href="#Strings">Strings</a></li>
        </ul>
    </li>
    <li>
        <a href="#UI">UI</a>
        <ul class="Topics">
            <li><a href="#CSS">CSS</a></li>
            <li><a href="#Icons">Icons</a></li>
        </ul>
    </li>
    <li>
        <a href="#Database">Database</a>
        <ul class="Topics">
            <li><a href="#SQL">SQL</a></li>
            <li><a href="#Models">Models</a></li>
        </ul>
    </li>
</ul>
<h3 id="CodingStyle">Coding style</h3>
<p>
    This section describes the conventions and requirements on source code.
</p>
<hr>
<h4 id="GeneralRecommendations">General recommendations</h4>
<ul>
    <li>
        Follow the "DRY", "KISS" and "YAGNI"(except utility-methods/-classes)-principles.
    </li>
    <li>Use "static" instead of "self" unless you want to explicitly reference the current class.</li>
    <li>Use "private" instead of "protected" unless you want to explicitly grant access to the extending class.</li>
    <li>Provide as much useful documentation as possible; code is read more often than written.</li>
    <li>Avoid instance-based utility methods, consider using static instead.</li>
    <li>Consider using generators instead of iterators for huge arrays.</li>
    <li>Consider providing interfaces over abstract classes.</li>
</ul>
<hr>
<h4 id="NamingConventions">Naming conventions</h4>
<p>
    vDesk strictly uses the "PascalCase"-notation except for the names of JavaScript <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent">CustomEvents</a>
    and a small amount of facades to built-in objects in the client (this may be refactored in the future?).<br>
    The purpose of this notation is to visually separate library code from runtime code, because vDesk provides many object-oriented interfaces for general tasks like database
    access or file manipulation.
</p>
<p>If your code extends a built-in class or prototype and you want to provide a consistent API, you may ignore this rule and use the "camelCase"-notation instead.<br>
    But please: no "snake_case"!</p>
<hr>
<h4 id="TypeCompliance">Type compliance</h4>
<p>
    vDesk strictly follows a strictly type-safe approach of development.<br>
    That means source code has to written as much type-safe as possible.
</p>
<h5>PHP</h5>
<p>
    Every sourcefile containing type-hints must begin with the <code class="Inline"><?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?></code>-statement.
</p>
<h5>JavaScript</h5>
<p>
    As of the weakly-typed nature of ECMA-/JavaScript, there's currently no equivalent of type-hinting.<br>
    However, if you <u>optionally</u> want to use value type-checking, you can use the <code class="Inline">Ensure.<?= Code::Function("Parameter") ?>()</code> and <code
            class="Inline">Ensure.<?= Code::Function("Property") ?>()</code> methods.
</p>
<hr>
<h4 id="CodeBlocks">Code blocks</h4>
<p>
    vDesk uses the "egyptian"-style placement for curly braces and square brackets.<br>
    This convention applies to every logical code block except inline and parameter array-initializers and object-literals.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Function ?> <?= Code::Function("Save") ?>()
{
    <?= Code::Variable("\$Values") ?> =
    [
        <?= Code::String("\"A\"") ?>,
        <?= Code::String("\"B\"") ?>,
        <?= Code::String("\"C\"") ?>
      
    ]<?= Code::Delimiter ?>
    
}
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Function ?> <?= Code::Function("Save") ?>() {
    <?= Code::Variable("\$Values") ?> = [
        <?= Code::String("\"A\"") ?>,
        <?= Code::String("\"B\"") ?>,
        <?= Code::String("\"C\"") ?>
      
    ]<?= Code::Delimiter ?>
    
}
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Function("MethodCall") ?>(
    <?= Code::Variable("\$Param") ?>,
    [
        <?= Code::String("\"A\"") ?>,
        <?= Code::String("\"B\"") ?>,
        <?= Code::String("\"C\"") ?>
      
    ],
    ...
)<?= Code::Delimiter ?>
</code></pre>
<hr>
<h4 id="VariablesFieldsConstants">Variables, Fields and Constants</h4>
<p>Variables, fields and constants must be named according to the value they yield. Avoid using general names.</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Variable("\$Data") ?>        = \vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Archive") ?>()::<?= Code::Function("GetBranch") ?>(<?= Code::Keyword("new") ?> <?= Code::Class("Element") ?>())<?= Code::Delimiter ?>
        
<?= Code::Constant ?> <?= Code::Variable("Values") ?> = vDesk.<?= Code::Class("Modules") ?>[<?= Code::String("\"Archive\"") ?>].<?= Code::Function("GetBranch") ?>(<?= Code::Keyword("new") ?> <?= Code::Class("Element") ?>())<?= Code::Delimiter ?>
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Variable("\$Branch") ?>      = \vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Archive") ?>()::<?= Code::Function("GetBranch") ?>(<?= Code::Keyword("new") ?> <?= Code::Class("Element") ?>())<?= Code::Delimiter ?>
        
<?= Code::Constant ?> <?= Code::Variable("Branch") ?> = vDesk.<?= Code::Class("Modules") ?>[<?= Code::String("\"Archive\"") ?>].<?= Code::Function("GetBranch") ?>(<?= Code::Keyword("new") ?> <?= Code::Class("Element") ?>())<?= Code::Delimiter ?>
</code></pre>
<p>Avoid useless variables/allocations.</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Function ?> <?= Code::Function("Foo") ?>() {
    <?= Code::Variable("\$Foo") ?> = <?= Code::Function("Bar") ?>()<?= Code::Delimiter ?>
        
    <?= Code::Keyword("return") ?> <?= Code::Variable("\$Foo") ?><?= Code::Delimiter ?>
    
}
</code></pre>
<p>Avoid long identifier names. Instead of writing an entire sentence, consider thinking of separating the concern of the identifier into a new class.</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Variable("\$Element") ?>-><?= Code::Class("OwnerName") ?><?= Code::Delimiter ?>
        
<?= Code::Variable("Element") ?>.<?= Code::Class("OwnerName") ?><?= Code::Delimiter ?>
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Variable("\$Element") ?>-><?= Code::Class("Owner") ?>-><?= Code::Class("Name") ?><?= Code::Delimiter ?>
        
<?= Code::Variable("Element") ?>.<?= Code::Class("Owner") ?>.<?= Code::Class("Name") ?><?= Code::Delimiter ?>
</code></pre>
<p>Avoid "screaming" constants.</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("CONSTANT_VALUE") ?><?= Code::Delimiter ?>
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("ConstantValue") ?><?= Code::Delimiter ?>
</code></pre>
<hr>
<h4 id="FunctionsMethods">Functions/Methods</h4>
<p>
    Function names should describe in a short manner the logic that the desired function represents.<br>
    Keep function names "stupid simple" and follow the "DRY" principle.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Class("Log") ?>::<?= Code::Function("WriteDebugLogMessage") ?>(<?= Code::Variable("\$Message") ?>)<?= Code::Delimiter ?>
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Class("Log") ?>::<?= Code::Function("Write") ?>(<?= Code::Class("Log") ?>::<?= Code::Const("Debug") ?>, <?= Code::Variable("\$Message") ?>)<?= Code::Delimiter ?>

<?= Code::Comment("//Or") ?>

<?= Code::Class("Log") ?>::<?= Code::Function("Debug") ?>(<?= Code::Variable("\$Message") ?>)<?= Code::Delimiter ?>
</code></pre>
<hr>
<h4 id="Parameters">Parameters</h4>
<p>Parameters must be named according their purpose. Basically, code should provide a "beautiful API"<?= Code::Delimiter ?> that means parameter names must not consist of
    abbreviations.</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Function ?> <?= Code::Function("InstallPackage") ?>(FileInfo <?= Code::Variable("\$Pkg") ?> = <?= Code::Null ?>)
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Function ?> <?= Code::Function("InstallPackage") ?>(FileInfo <?= Code::Variable("\$Package") ?> = <?= Code::Null ?>)
</code></pre>
<hr>
<h4 id="Classes">Classes</h4>
<p>
    Classes must be named as singular (agent) nouns or imperatives and reflect their purposes.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Namespace ?> vDesk\Security<?= Code::Delimiter ?>
        
<?= Code::ClassDeclaration ?> <?= Code::Class("AccessControlListEntry") ?> {}

vDesk.Calendar.<?= Code::Class("EventEditor") ?> = <?= Code::Function ?> <?= Code::Class("EventEditor") ?>() {}
</code></pre>
<h5>Purpose:</h5>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Namespace ?> vDesk\Security\AccessControlList<?= Code::Delimiter ?>
        
<?= Code::ClassDeclaration ?> <?= Code::Class("Entry") ?> {}

vDesk.Calendar.Event.<?= Code::Class("Editor") ?> = <?= Code::Function ?> <?= Code::Class("Editor") ?>() {}
</code></pre>
<h5>Imperative:</h5>
<pre><code><?= Conventions::Recommended ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("BinaryReader") ?> {}
</code></pre>
<hr>
<h4 id="Interfaces">Interfaces</h4>
<p>
    Interfaces must be named as (agent) nouns or adjectives starting with a capital "I" letter and reflect their purposes.<br>
    <br>
    For a JavaScript based code example, visit the "<a href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", "ClassicalInheritance#Interfaces") ?>">Classical
        inheritance and interfaces in JavaScript</a>"-tutorial.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Interface ?> <?= Code::Class("ModelInterface") ?> {}
<?= Code::Interface ?> <?= Code::Class("IEnumerated") ?> {}
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Interface ?> <?= Code::Class("IModel") ?> {}
<?= Code::Interface ?> <?= Code::Class("IEnumerable") ?> {}
</code></pre>
<hr>
<h4 id="Traits">Traits</h4>
<p>
    Traits must be named as imperatives and reflect their purposes.<br>
    Generally it's a good idea trying to avoid using traits, consider using proper class (hierarchy) instead.<br>
    If you want to use traits anyway, design them independent of any implementation details;<br>
    otherwise, if your trait requires a dependency, provide at least an appropriate abstract method or interface.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Trait ?> <?= Code::Class("AccessController") ?> {}
<?= Code::Trait ?> <?= Code::Class("VersionControlled") ?> {}
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Trait ?> <?= Code::Class("AccessControl") ?> {}
<?= Code::Trait ?> <?= Code::Class("VersionControl") ?> {}
</code></pre>
<hr>
<h4 id="Namespaces">Namespaces</h4>
<p>
    Namespaces must be named as singular (agent) nouns, imperatives or adjectives representing the functionality it contains.
</p>
<p>
    JavaScript namespaces have to be declared as plain object's described by a JSDocBlock.
    Namespaces must be named as imperatives and reflect their purposes.<br>
</p>
<pre><code><?= Code\Language::JS ?>
<?= Code::BlockComment("/**
 * @namespace Space
 * @memberOf My.Name
 */") ?>
        
<?= Code::Variable("My") ?>.<?= Code::Field("Name") ?>.<?= Code::Field("Space") ?> = {}<?= Code::Delimiter ?>
</code></pre>
<p>
    Classes can be used as namespaces too.
</p>
<pre><code><?= Code\Language::JS ?>
<?= Code::Variable("NameSpace") ?>.<?= Code::Field("SubNameSpace") ?>          = <?= Code::ClassDeclaration ?> <?= Code::Class("SubNameSpace") ?> {}<?= Code::Delimiter ?>
        
<?= Code::Variable("NameSpace") ?>.<?= Code::Field("SubNameSpace") ?>.<?= Code::Field("SubClass") ?> = <?= Code::Function ?> <?= Code::Class("SubClass") ?> {}<?= Code::Delimiter ?>
</code></pre>
<p>
    PHP namespaces must follow the <a target="_blank" href="https://www.php-fig.org/psr/psr-4/">PSR-4</a> standard unless you want to provide a custom autoloader callback.
</p>
<pre><code><?= Code\Language::PHP ?>
<?= Code::Namespace ?> My\Name\Space<?= Code::Delimiter ?>
</code></pre>
<hr>
<h4 id="ErrorHandling">Error handling</h4>
<p>Consider using the "crash early" pattern.</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Function ?> <?= Code::Function("InstallPackage") ?>(<?= Code::Class("Package") ?> <?= Code::Variable("\$Package") ?>): <?= Code::Class("Package") ?> {
    
    <?= Code::Comment("//Install Package..") ?>
        
    <?= Code::Variable("\$Package") ?>::<?= Code::Function("Install") ?>()<?= Code::Delimiter ?>
        
        
    <?= Code::If ?>(!\<?= Code::Class("vDesk") ?>::<?= Code::Variable("\$User") ?>-><?= Code::Field("Permissions") ?>[<?= Code::String("\"InstallPackage\"") ?>]) {
        <?= Code::Class("Log") ?>::<?= Code::Function("Warn") ?>(<?= Code::Const("__METHOD__") ?>, \<?= Code::Class("vDesk") ?>::<?= Code::Variable("\$User") ?>-><?= Code::Field("Name") ?> . <?= Code::String("\" tried to install Package without having permissions.\"") ?>);
        <?= Code::Throw ?> <?= Code::New ?> <?= Code::Class("UnauthorizedAccessException") ?>();
    }
    
}
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Function ?> <?= Code::Function("InstallPackage") ?>(<?= Code::Class("Package") ?> <?= Code::Variable("\$Package") ?>): <?= Code::Class("Package") ?> {
    
    <?= Code::If ?>(!\<?= Code::Class("vDesk") ?>::<?= Code::Variable("\$User") ?>-><?= Code::Field("Permissions") ?>[<?= Code::String("\"InstallPackage\"") ?>]) {
        <?= Code::Class("Log") ?>::<?= Code::Function("Warn") ?>(<?= Code::Const("__METHOD__") ?>, \<?= Code::Class("vDesk") ?>::<?= Code::Variable("\$User") ?>-><?= Code::Field("Name") ?> . <?= Code::String("\" tried to install Package without having permissions.\"") ?>);
        <?= Code::Throw ?> <?= Code::New ?> <?= Code::Class("UnauthorizedAccessException") ?>();
    }
    
    <?= Code::Comment("//Install Package..") ?>
        
    <?= Code::Variable("\$Package") ?>::<?= Code::Function("Install") ?>()<?= Code::Delimiter ?>
    
    
}
</code></pre>
<hr>
<h4 id="Properties">Properties</h4>
<p>
    Consider using properties and following the "information hiding"-principle.
</p>
<pre><code><?= Code\Language::JS ?>
<?= Code::Keyword("function") ?> <?= Code::Class("Example") ?>(){
    
    <?= Code::Keyword("let") ?> <?= Code::Variable("PrivateMember") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("\"PublicMember\"") ?>,{
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>:        () => <?= Code::Variable("PrivateMember") ?>,
        <?= Code::Function("set") ?>:        Value => <?= Code::Variable("PrivateMember") ?> = Value
    })<?= Code::Delimiter ?>
        
        
    <?= Code::Keyword("const") ?> <?= Code::Const("Greeting") ?> = <?= Code::Class("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"h1\"") ?>)<?= Code::Delimiter ?>
        
    <?= Code::Keyword("let") ?> <?= Code::Variable("Name") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperties") ?>(<?= Code::Keyword("this") ?>, {
        <?= Code::Variable("Name") ?>: {
            <?= Code::Variable("enumerable") ?>:   <?= Code::Bool("true") ?>,
            <?= Code::Variable("configurable") ?>: <?= Code::Bool("false") ?>,
            <?= Code::Function("get") ?>:          () => <?= Code::Variable("Name") ?>,
            <?= Code::Function("set") ?>:          Value => {
                <?= Code::Variable("Name") ?> = Value<?= Code::Delimiter ?>
        
                <?= Code::Const("Greeting") ?>.<?= Code::Field("textContent") ?> = <?= Code::String("`Greetings, \"") ?>${Value}<?= Code::String("\"!`") ?><?= Code::Delimiter ?>
        
            }
        },
        <?= Code::Variable("Further") ?>: ...
    })<?= Code::Delimiter ?>
        
        
}
</code></pre>
<pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> Example {
    
    <?= Code::Use ?> \vDesk\Struct\<?= Code::Class("Properties") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Private ?> ?string <?= Code::Field("\$PrivateMember") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?> {
        
        <?= Code::Comment("//Definition of a single property.") ?>
        
        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperty") ?>(
            <?= Code::String("\"PublicMember\"") ?>,
            [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?string => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?>,
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(string <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?> = <?= Code::Variable("\$Value") ?>
          
            ]
        )<?= Code::Delimiter ?>
        
        
        <?= Code::Comment("//Chained definition of a single property.") ?>
        
        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperty") ?>(<?= Code::String("\"PublicMember\"") ?>)
             -><?= Code::Function("Get") ?>(<?= Code::Keyword("fn") ?>(): ?string => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?>)
             -><?= Code::Function("Set") ?>(<?= Code::Keyword("fn") ?>(string <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?> = <?= Code::Variable("\$Value") ?>)<?= Code::Delimiter ?>
        
        
        <?= Code::Comment("//Defining multiple properties.") ?>
        
        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperties") ?>([
            <?= Code::String("\"PublicMember\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?string => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?>,
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(string <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?> = <?= Code::Variable("\$Value") ?>
          
            ],
            <?= Code::String("\"Further\"") ?> => ...
        ])<?= Code::Delimiter ?>
        
        
    }
    
}
</code></pre>
<hr>
<h4 id="Iteration">Iteration</h4>
<h5>JavaScript</h5>
<p>
    Use <code class="Inline"><?= Code::Class("Array") ?>.<?= Code::Field("prototype") ?>.<a target="_blank"
                                                                                            href="https://developer.mozilla.org/de/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach"><?= Code::Function("forEach") ?></a></code>
    in combination with lambda predicates over "for" and "while".
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Constant ?> <?= Code::Const("Values") ?> = [<?= Code::Int("1") ?>, <?= Code::Int("2") ?>, <?= Code::Int("3") ?>]<?= Code::Delimiter ?>
        
        
<?= Code::For ?>(<?= Code::Let ?> <?= Code::Variable("Index") ?> = <?= Code::Int("0") ?><?= Code::Delimiter ?> <?= Code::Variable("Index") ?> <= <?= Code::Const("Values") ?>.<?= Code::Field("length") ?><?= Code::Delimiter ?> <?= Code::Variable("Index") ?>++) {
    <?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Const("Values") ?>[<?= Code::Variable("Index") ?>])<?= Code::Delimiter ?>
    
}
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Constant ?> <?= Code::Const("Values") ?> = [<?= Code::Int("1") ?>, <?= Code::Int("2") ?>, <?= Code::Int("3") ?>]<?= Code::Delimiter ?>
        
        
<?= Code::Const("Values") ?>.<?= Code::Function("forEach") ?>(Value => <?= Code::Class("console") ?>.<?= Code::Function("log") ?>(Value))<?= Code::Delimiter ?>
</code></pre>
<h5>PHP</h5>
<p>
    Use "foreach" over "for" and "while".<br>
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Variable("\$Values") ?> = [<?= Code::Int("1") ?>, <?= Code::Int("2") ?>, <?= Code::Int("3") ?>]<?= Code::Delimiter ?>
        
<?= Code::Variable("\$Index") ?>  = <?= Code::Int("0") ?><?= Code::Delimiter ?>
        
        
<?= Code::While ?>(<?= Code::Variable("\$Index") ?> <= \<?= Code::Function("count") ?>(<?= Code::Const("\$Values") ?>)) {
    \<?= Code::Function("var_dump") ?>(<?= Code::Variable("\$Values") ?>[++<?= Code::Variable("\$Index") ?>])<?= Code::Delimiter ?>
    
}
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Variable("\$Values") ?> = [<?= Code::String("\"One\"") ?> => <?= Code::Int("1") ?>, <?= Code::String("\"Two\"") ?> => <?= Code::Int("2") ?>, <?= Code::String("\"Three\"") ?> => <?= Code::Int("3") ?>]<?= Code::Delimiter ?>
        
        
<?= Code::ForEach ?>(<?= Code::Variable("\$Values") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Key") ?> => <?= Code::Variable("\$Value") ?>) {
    \<?= Code::Function("var_dump") ?>(<?= Code::Variable("\$Key") ?>, <?= Code::Variable("\$Value") ?>)<?= Code::Delimiter ?>
    
}
</code></pre>
<hr>
<h4 id="Arrays">Arrays</h4>
<p>
    Use the square bracket syntax instead of the "array()" language construct.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Variable("\$Values") ?> = <?= Code::Keyword("array") ?>(<?= Code::String("\"A\"") ?>, <?= Code::String("\"B\"") ?>, <?= Code::String("\"C\"") ?>)<?= Code::Delimiter ?>
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Variable("\$Values") ?> = [<?= Code::String("\"A\"") ?>, <?= Code::String("\"B\"") ?>, <?= Code::String("\"C\"") ?>]<?= Code::Delimiter ?>
</code></pre>
<p>
    Use the square bracket syntax for array destructuring instead of the "list()" language construct.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Variable("\$Value") ?> = <?= Code::Keyword("list") ?>(<?= Code::Variable("\$Values") ?>)<?= Code::Delimiter ?>
</code></pre>
<pre><code><?= Conventions::Recommended ?>
[<?= Code::Variable("\$Value") ?>] = <?= Code::Variable("\$Values") ?><?= Code::Delimiter ?>
</code></pre>
<hr>
<h4 id="Strings">Strings</h4>
<p>
    Use double quotes instead of single quotes for string literals.<br>
    Consider using template-syntax for larger JavaScript strings.<br>
    Consider using Nowdoc-/Heredoc-syntax for larger PHP strings.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Constant ?> <?= Code::Const("Text") ?> = <?= Code::String("'Lorem ipsum dolor sit amet..'") ?><?= Code::Delimiter ?>
        
<?= Code::Variable("\$Text") ?>      = <?= Code::String("'Lorem ipsum dolor sit amet..'") ?><?= Code::Delimiter ?>
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Constant ?> <?= Code::Const("Text") ?>         = <?= Code::String("\"Lorem ipsum dolor sit amet..\"") ?><?= Code::Delimiter ?>


<?= Code::Constant ?> <?= Code::Const("Interpolated") ?> = <?= Code::String("`Lorem ") ?>${<?= Code::Variable("Ipsum") ?>}<?= Code::String(" dolor sit amet..`") ?><?= Code::Delimiter ?>


<?= Code::Constant ?> <?= Code::Const("MultiLine") ?>    = <?= Code::String("`
    Lorem ipsum
    dolor
    sit amet..
`") ?><?= Code::Delimiter ?>
        
        
<?= Code::Variable("\$Text") ?>         = <?= Code::String("\"Lorem ipsum dolor sit amet..\"") ?><?= Code::Delimiter ?>
        
        
<?= Code::Variable("\$Interpolated") ?> = <?= Code::String("\"Lorem ") ?>{<?= Code::Variable("\$Ipsum") ?>}<?= Code::String(" dolor sit amet..\"") ?><?= Code::Delimiter ?>
        
        
<?= Code::Variable("\$MultiLine") ?>    = &lt;&lt;&lt;Text
<?= Code::String("    Lorem ipsum
    dolor
    sit amet..") ?>
    
Text<?= Code::Delimiter ?>
</code></pre>
<h3 id="UI">UI</h3>
<p>This section describes visual guidelines.</p>
<p>
    As a general rule for frontend-development, is to keep the user interface as simple as possible.<br>
    Consider the following rules as a "recommendation" for designing graphical interfaces.
</p>
<hr>
<h4 id="CSS">CSS</h4>
<p>
    CSS-classes must be named like the control's class they represent, located in a namespace-equivalent directory.<br>
    If a classname intersects with a different one, consider using combined classnames for CSS-classes consisting of the bottom-most namespace and control's classname.<br>
    Focus the viewmodel the current namespace concerns in order of least combined classnames.<br>
    For example,
    Try to use the least combined classnames for
    Use cascading styles to design controls while re-using as much CSS-classes as possible.<br>
</p>
<hr>
<h4 id="Icons">Icons</h4>
<p>Until the system is capable of handling and <a href="<?= Functions::URL("vDesk", "Page", "Roadmap#Colors") ?>">coloring SVG-images</a>, icons have to meet the following
    conditions:</p>
<ul>
    <li>It's either from the <a target="_blank" href="https://icons8.de/icons/metro">Windows Metro</a> icon pack from <a target="_blank" href="https://icons8.de">icons8.com</a> or
        generally suits the look&feel of the UI
    </li>
    <li>Provided in the portable network graphics(png) format</li>
    <li>Recolored to <span style="color: #333333">#333333</span></li>
    <li>Base64 encoded and stored in the <code class="Inline">vDesk.Visual.Icons</code>-namespace</li>
    <li>Dimensions of at least 48x48 pixels</li>
    <li>Must not violate any licenses or cause any copyright infringements</li>
</ul>
<h3 id="Database">Database</h3>
<p>
    This section describes the access on SQL-database servers.
</p>
<hr>
<h4 id="SQL">SQL</h4>
<p>
    Although the "Structured Query Language" already provides an abstract way of communicating with database servers, there are still exist some inconsistencies between certain
    RDBMS.<br>
    These differences occur mostly while working with schemas, tables, column definitions and datatypes and are usually minor implementation details.<br>
    So it still may happen that for example database "A" only supports the "MODIFY"-kewyord, while database "B" only supports the "ALTER"-keyword while database "C" supports both,
    and so on..
</p>
<p>
    To address the common differences between several RDBMs, vDesk's dataprovider ships with an <a href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", "Expressions") ?>">Expression</a>-library, that allows the developer to "express" the operation you want
    to perform against the database server in a parametrized monade that translates the action into a query that is compatible to the current configured database system.
    The expression library covers most oft the usual CRUD and DB/Table-manipulation functions that SQL defines.
</p>
<p>
    If you're currently missing a feature and as long as your code only consists of standard conform SQL-statements,<br>
    you may pass any plain SQL-strings via the <code class="Inline"><?= Code::Class("DataProvider") ?>::<?= Code::Function("Execute") ?>()</code>-method directly to the underlying
    driver (like <a target="_blank" href="https://www.php.net/manual/de/mysqli.real-query.php">mysqli::real_query()</a>, <a target="_blank"
                                                                                                                            href="https://www.php.net/manual/de/function.pg-query.php">pg_query()</a>
    or <a target="_blank" href="https://www.php.net/manual/de/pdo.query.php">PDO::query()</a>.<br>
    Anyway, consider at least opening an issue on <a href="https://www.github.com/vDesk-Cloud">Github</a> or implementing the required function into the expression-library.
</p>
<p>
    In general, it is a good idea sticking to the expressions-library instead of executing plain SQL against the database server.
</p>
<pre><code><?= Conventions::NotRecommended ?>
<?= Code::Variable("\$ResultSet") ?> = \vDesk\<?= Code::Class("DataProvider") ?>::<?= Code::Function("Execute") ?>(<?= Code::String("\"SELECT * FROM Table WHERE ID = 1\"") ?>)<?= Code::Delimiter ?>
</code></pre>
<pre><code><?= Conventions::Recommended ?>
<?= Code::Variable("\$ResultSet") ?> = \vDesk\DataProvider\<?= Code::Class("Expression") ?>::<?= Code::Function("Select") ?>(<?= Code::String("\"*\"") ?>)
                                           -><?= Code::Function("From") ?>(<?= Code::String("\"Table\"") ?>)
                                           -><?= Code::Function("Where") ?>([<?= Code::String("\"ID\"") ?> => <?= Code::Int("1") ?>])
                                           -><?= Code::Function("Execute") ?>()<?= Code::Delimiter ?>
</code></pre>
<hr>
<h4 id="Models">Models</h4>
<p>
    This is a general recommendation for designing database Models.
</p>
<p>
    As of the object-oriented nature of vDesk, the datastorage concept behind the system relies on "rich"-models.<br>
    That means, that any current model is capable of the following functionality:
</p>
<ol>
    <li>Typesafe getter- and setter-methods</li>
    <li>Lazy loading values of single properties from the database</li>
    <li>Creating, updating and deleting database records</li>
    <li>Formatting its current state into a JSON-encodable representation</li>
</ol>
<p>
    If your Model references a dependency Model or database value, consider using mapped getters/setters instead.
</p>
<h5>Example model class</h5>
<pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> Model <?= Code::Implements ?> \vDesk\Data\IModel {
    
    <?= Code::Use ?> \vDesk\Struct\<?= Code::Class("Properties") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Private ?> ?Dependency <?= Code::Field("\$Dependency") ?> = <?= Code::Null ?><?= Code::Delimiter ?>
    
    
    <?= Code::Private ?> ?int <?= Code::Field("\$DependencyValue") ?> = <?= Code::Null ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?> {
        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperties") ?>([
            <?= Code::String("\"Mapped\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Class("MappedGetter") ?>::<?= Code::Function("Create") ?>(
                    <?= Code::Variable("\$this") ?>-><?= Code::Field("Dependency") ?>,
                    <?= Code::Class("Dependency") ?>::<?= Code::Const("class") ?>,
                    <?= Code::Bool("true") ?>,
                    <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>,
                    <?= Code::Class("Expression") ?>::<?= Code::Function("Select") ?>(<?= Code::String("\"Dependency\"") ?>)
                              -><?= Code::Function("From") ?>(<?= Code::String("\"Table\"") ?>)
                ),
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(Dependency <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("Dependency") ?> = <?= Code::Variable("\$Value") ?>
          
            ],
            <?= Code::String("\"Manual\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?int => <?= Code::Return ?> <?= Code::Variable("\$this") ?>-><?= Code::Field("DependencyValue") ?> ??= (int)<?= Code::Class("Expression") ?>::<?= Code::Function("Select") ?>(<?= Code::String("\"DependencyValue\"") ?>)
                                                                                       -><?= Code::Function("From") ?>(<?= Code::String("\"Table\"") ?>)
                                                                                       -><?= Code::Function("Where") ?>([<?= Code::String("\"ID\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>])(),
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(int <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("DependencyValue") ?> = <?= Code::Variable("\$Value") ?>
          
            ]
        ])<?= Code::Delimiter ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ID") ?>(): ?int {
        <?= Code::Return ?> <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?><?= Code::Delimiter ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Fill") ?>(): Model {
        <?= Code::Comment("//Fill model with database record..") ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Save") ?>(): void {
        <?= Code::Comment("//Create or update database record..") ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Delete") ?>(): void {
        <?= Code::Comment("//Delete associated database record..") ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("FromDataView") ?>(array <?= Code::Variable("\$DataView") ?>): Model {
        <?= Code::Return ?> <?= Code::New ?> <?= Code::Static ?>(
            <?= Code::Variable("\$DataView") ?>[<?= Code::String("\"ID\"") ?>] ?? <?= Code::Null ?>,
            <?= Code::Class("Dependency") ?>::<?= Code::Function("FromDataView") ?>(<?= Code::Variable("\$DataView") ?>),
            ...
        )<?= Code::Delimiter ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ToDataView") ?>(): array {
        <?= Code::Return ?> [
            <?= Code::String("\"ID\"") ?>         => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>,
            <?= Code::String("\"Dependency\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Dependency") ?>-><?= Code::Function("ToDataView") ?>(),
            ...
        ]<?= Code::Delimiter ?>
        
    }
    
}
</code></pre>