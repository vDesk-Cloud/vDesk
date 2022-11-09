<?php
use vDesk\Documentation\Code;
use vDesk\Documentation\Code\Conventions;
use vDesk\Pages\Functions;
?>
<article class="Development">
    <header>
        <h2>Development</h2>
        <p>
            This document describes the general coding conventions of vDesk's source code.<br>
            Before submitting a pull request, consider reading the following specifications.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li>
                <a href="#LanguageLevels">Language levels</a>
            </li>
            <li>
                <a href="#CodingStyle">Coding style</a>
                <ul class="Topics">
                    <li><a href="#GeneralRecommendations">General recommendations</a></li>
                    <li><a href="#CodeNamingConventions">Naming conventions</a></li>
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
                    <li><a href="#Keywords">Keywords</a></li>
                </ul>
            </li>
            <li>
                <a href="#UI">User interface</a>
                <ul class="Topics">
                    <li><a href="#CSS">CSS</a></li>
                    <li><a href="#Colors">Colors</a></li>
                    <li><a href="#Icons">Icons</a></li>
                </ul>
            </li>
            <li>
                <a href="#Database">Database</a>
                <ul class="Topics">
                    <li><a href="#SQL">SQL</a></li>
                    <li><a href="#DatabaseNamingConventions">Naming conventions</a></li>
                    <li><a href="#Models">Models</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <section id="LanguageLevels">
        <h3>Language levels</h3>
        <table>
            <tr>
                <th>Language/runtime</th>
                <th>Minimum version</th>
                <th>Recommended version</th>
            </tr>
            <tr>
                <td>JavaScript</td>
                <td>ECMAScript 2020</td>
                <td>ECMAScript 2020 or higher</td>
            </tr>
            <tr>
                <td>CSS</td>
                <td>Custom Properties Level 1</td>
                <td>Custom Properties Level 1 or higher</td>
            </tr>
            <tr>
                <td>PHP</td>
                <td>8.0</td>
                <td>8.1 or higher</td>
            </tr>
            <tr>
                <td>MySQL</td>
                <td>5.6</td>
                <td>5.7 or higher</td>
            </tr>
            <tr>
                <td>PostgreSQL</td>
                <td>13</td>
                <td>13 or higher</td>
            </tr>
            <tr>
                <td>MS SQL Server</td>
                <td>2019</td>
                <td>2019 or higher</td>
            </tr>
        </table>
    </section>
    <section id="CodingStyle">
        <h3>Coding style</h3>
        <p>
           Source code files must be must be encoded in UTF-8 without a BOM and use a combination of carriage-return&linefeed characters (\r\n) as a line delimiter.
        </p>
        <hr>
        <h4 id="GeneralRecommendations">General recommendations</h4>
        <ul>
            <li>Follow the "DRY", "KISS" and "YAGNI"(except utility-methods/-classes)-principles.</li>
            <li>Use "static" instead of "self" unless you want to explicitly reference the current class.</li>
            <li>Use "protected" instead of "private" unless you want to explicitly restrict access to extending classes.</li>
            <li>Provide as much useful documentation as possible; code is read more often than written.</li>
            <li>Avoid instance-based utility methods, consider using static methods instead.</li>
            <li>Consider using generators instead of iterators for huge arrays.</li>
            <li>Consider providing interfaces over abstract classes.</li>
        </ul>
    </section>
    <section id="CodeNamingConventions">
        <h4>Naming conventions</h4>
        <p>
            vDesk strictly uses the "PascalCase"-notation except for the names of JavaScript <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent">CustomEvents</a>
            and a small amount of facades to built-in objects in the client (this may be refactored in the future?).<br>
            The purpose of this notation is to visually separate library code from runtime code, because vDesk provides many object-oriented interfaces for general tasks like database
            access or file manipulation.
        </p>
        <p>
            If your code extends a built-in class or prototype and you want to provide a consistent API, you may ignore this rule and use the "camelCase"-notation instead.<br>
            But please: no "snake_case"!
        </p>
    </section>
    <section id="TypeCompliance">
        <h4>Type compliance</h4>
        <p>
            vDesk follows a strictly type-safe approach of development.<br>
            That means source code has to written as much type-safe as possible.
        </p>
        <h5>PHP</h5>
        <p>
            Every source file containing type-hints must begin with the <code class="Inline"><?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?></code>-statement.
        </p>
        <h5>JavaScript</h5>
        <p>
            As of the weakly-typed nature of ECMA-/JavaScript, there's currently no equivalent of type-hinting.<br>
            However, if you <u>optionally</u> want to use value type-checking, you can use the <code class="Inline">Ensure.<?= Code::Function("Parameter") ?>()</code> and
            <code class="Inline">Ensure.<?= Code::Function("Property") ?>()</code> methods.
        </p>
        <p>
            At least, source files have to use the <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Strict_mode">strict mode</a>
            and must begin with the <code class="Inline"><?= Code::String("\"use strict\"") ?><?= Code::Delimiter ?></code>-statement.
        </p>
    </section>
    <section id="CodeBlocks">
        <h4>Code blocks</h4>
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
    </section>
    <section id="VariablesFieldsConstants">
        <h4>Variables, Fields and Constants</h4>
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
    </section>
    <section id="FunctionsMethods">
        <h4>Functions/Methods</h4>
        <p>
            Function names should describe in a short manner the logic they represent.<br>
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
    </section>
    <section id="Parameters">
        <h4 id="Parameters">Parameters</h4>
        <p>
            Parameters must be named according their purpose. Basically, code should provide a "beautiful API"<?= Code::Delimiter ?> that means parameter names must not consist of abbreviations.
        </p>
        <pre><code><?= Conventions::NotRecommended ?>
<?= Code::Function ?> <?= Code::Function("InstallPackage") ?>(<?= Code::Class("FileInfo") ?> <?= Code::Variable("\$Pkg") ?> = <?= Code::Null ?>)
</code></pre>
        <pre><code><?= Conventions::Recommended ?>
<?= Code::Function ?> <?= Code::Function("InstallPackage") ?>(<?= Code::Class("FileInfo") ?> <?= Code::Variable("\$Package") ?> = <?= Code::Null ?>)
</code></pre>
    </section>
    <section id="Classes">
        <h4>Classes</h4>
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
    </section>
    <section id="Interfaces">
        <h4>Interfaces</h4>
        <p>
            Interfaces must be named as (agent) nouns or adjectives starting with a capital "I" letter and reflect their purposes.<br>
            <br>
            For a JavaScript based code example, visit the "<a href="<?= Functions::URL("Documentation", "Category", "Client", "Topic", "ClassicalInheritance#Interfaces") ?>">Classical
                inheritance and interfaces in JavaScript</a>"-section.
        </p>
        <pre><code><?= Conventions::NotRecommended ?>
<?= Code::Interface ?> <?= Code::Class("ModelInterface") ?> {}
<?= Code::Interface ?> <?= Code::Class("IEnumerated") ?> {}
</code></pre>
        <pre><code><?= Conventions::Recommended ?>
<?= Code::Interface ?> <?= Code::Class("IModel") ?> {}
<?= Code::Interface ?> <?= Code::Class("IEnumerable") ?> {}
</code></pre>
    </section>
    <section id="Traits">
        <h4>Traits</h4>
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
    </section>
    <section id="Namespaces">
        <h4>Namespaces</h4>
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
    </section>
    <section id="ErrorHandling">
        <h4>Error handling</h4>
        <p>Consider using the "crash early" pattern.</p>
        <pre><code><?= Conventions::NotRecommended ?>
<?= Code::Function ?> <?= Code::Function("InstallPackage") ?>(<?= Code::Class("Package") ?> <?= Code::Variable("\$Package") ?>): <?= Code::Class("Package") ?> {
    
    <?= Code::If ?>(<?= Code::Class("User") ?>::<?= Code::Variable("\$Current") ?>-><?= Code::Field("Permissions") ?>[<?= Code::String("\"InstallPackage\"") ?>]) {

        <?= Code::Comment("//Install Package.") ?>

        <?= Code::Variable("\$Package") ?>::<?= Code::Function("Install") ?>()<?= Code::Delimiter ?>


        <?= Code::Comment("//Lots of code...") ?>


    } <?= Code::Else ?> {
        <?= Code::Class("Log") ?>::<?= Code::Function("Warn") ?>(<?= Code::Const("__METHOD__") ?>, <?= Code::Class("User") ?>::<?= Code::Variable("\$Current") ?>-><?= Code::Field("Name") ?> . <?= Code::String("\" tried to install Package without having permissions.\"") ?>);
        <?= Code::Throw ?> <?= Code::New ?> <?= Code::Class("UnauthorizedAccessException") ?>();
    }
    
}
</code></pre>
        <pre><code><?= Conventions::Recommended ?>
<?= Code::Function ?> <?= Code::Function("InstallPackage") ?>(<?= Code::Class("Package") ?> <?= Code::Variable("\$Package") ?>): <?= Code::Class("Package") ?> {
    
    <?= Code::If ?>(!<?= Code::Class("User") ?>::<?= Code::Variable("\$Current") ?>-><?= Code::Field("Permissions") ?>[<?= Code::String("\"InstallPackage\"") ?>]) {
        <?= Code::Class("Log") ?>::<?= Code::Function("Warn") ?>(<?= Code::Const("__METHOD__") ?>, <?= Code::Class("User") ?>::<?= Code::Variable("\$Current") ?>-><?= Code::Field("Name") ?> . <?= Code::String("\" tried to install Package without having permissions.\"") ?>);
        <?= Code::Throw ?> <?= Code::New ?> <?= Code::Class("UnauthorizedAccessException") ?>();
    }
    
    <?= Code::Comment("//Install Package.") ?>
        
    <?= Code::Variable("\$Package") ?>::<?= Code::Function("Install") ?>()<?= Code::Delimiter ?>


    <?= Code::Comment("//Lots of code...") ?>

}
</code></pre>
    </section>
    <section id="Properties">
        <h4>Properties</h4>
        <p>
            Consider using properties and following the "information hiding"-principle.
        </p>
        <pre><code><?= Code\Language::JS ?>
<?= Code::Keyword("function") ?> <?= Code::Class("Example") ?>(){
    
    <?= Code::Keyword("let") ?> <?= Code::Variable("PrivateMember") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("\"PublicMember\"") ?>,{
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>:        () => <?= Code::Variable("PrivateMember") ?>,
        <?= Code::Function("set") ?>:        <?= Code::Variable("Value") ?> => <?= Code::Variable("PrivateMember") ?> = <?= Code::Variable("Value") ?>
        
    })<?= Code::Delimiter ?>
        
        
    <?= Code::Keyword("const") ?> <?= Code::Const("Greeting") ?> = <?= Code::Class("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"h1\"") ?>)<?= Code::Delimiter ?>
        
    <?= Code::Keyword("let") ?> <?= Code::Variable("Name") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperties") ?>(<?= Code::Keyword("this") ?>, {
        <?= Code::Variable("Name") ?>: {
            <?= Code::Variable("enumerable") ?>:   <?= Code::Bool("true") ?>,
            <?= Code::Variable("configurable") ?>: <?= Code::Bool("false") ?>,
            <?= Code::Function("get") ?>:          () => <?= Code::Variable("Name") ?>,
            <?= Code::Function("set") ?>:          <?= Code::Variable("Value") ?> => {
                <?= Code::Variable("Name") ?> = <?= Code::Variable("Value") ?><?= Code::Delimiter ?>
        
                <?= Code::Const("Greeting") ?>.<?= Code::Field("textContent") ?> = <?= Code::String("`Greetings, \"") ?>${<?= Code::Variable("Value") ?>}<?= Code::String("\"!`") ?><?= Code::Delimiter ?>
        
            }
        },
        <?= Code::Variable("Further") ?>: ...
    })<?= Code::Delimiter ?>
        
        
}
</code></pre>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("Example") ?> {
    
    <?= Code::Use ?> \vDesk\Struct\<?= Code::Class("Properties") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Private ?> ?<?= Code::Keyword("string") ?> <?= Code::Variable("\$PrivateMember") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?> {
        
        <?= Code::Comment("//Definition of a single property.") ?>
        
        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperty") ?>(
            <?= Code::String("\"PublicMember\"") ?>,
            [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?<?= Code::Keyword("string") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?>,
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?> = <?= Code::Variable("\$Value") ?>
          
            ]
        )<?= Code::Delimiter ?>
        
        
        <?= Code::Comment("//Chained definition of a single property.") ?>
        
        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperty") ?>(<?= Code::String("\"PublicMember\"") ?>)
             -><?= Code::Function("Get") ?>(<?= Code::Keyword("fn") ?>(): ?<?= Code::Keyword("string") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?>)
             -><?= Code::Function("Set") ?>(<?= Code::Keyword("fn") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?> = <?= Code::Variable("\$Value") ?>)<?= Code::Delimiter ?>
        
        
        <?= Code::Comment("//Defining multiple properties.") ?>
        
        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperties") ?>([
            <?= Code::String("\"PublicMember\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?<?= Code::Keyword("string") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?>,
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("PrivateMember") ?> = <?= Code::Variable("\$Value") ?>
          
            ],
            <?= Code::String("\"Further\"") ?> => ...
        ])<?= Code::Delimiter ?>
        
        
    }
    
}
</code></pre>
    </section>
    <section id="Iteration">
        <h4>Iteration</h4>
        <h5>JavaScript</h5>
        <p>
            Use <code class="Inline"><?= Code::Class("Array") ?>.<?= Code::Field("prototype") ?>.<a target="_blank" href="https://developer.mozilla.org/de/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach"><?= Code::Function("forEach") ?></a></code>
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
        
        
<?= Code::Const("Values") ?>.<?= Code::Function("forEach") ?>(<?= Code::Variable("Value") ?> => <?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Variable("Value") ?>))<?= Code::Delimiter ?>
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
    </section>
    <section id="Arrays">
        <h4>Arrays</h4>
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
<?= Code::Keyword("list") ?>(<?= Code::Variable("\$A") ?>, <?= Code::Variable("\$B") ?>, <?= Code::Variable("\$C") ?>) = <?= Code::Variable("\$Values") ?><?= Code::Delimiter ?>
</code></pre>
        <pre><code><?= Conventions::Recommended ?>
[<?= Code::Variable("\$A") ?>, <?= Code::Variable("\$B") ?>, <?= Code::Variable("\$C") ?>] = <?= Code::Variable("\$Values") ?><?= Code::Delimiter ?>
</code></pre>
    </section>
    <section id="Strings">
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
    </section>
    <section id="Keywords">
        <h4>Keywords</h4>
        <p>
            Keywords have to be written in lowercase, except for referencing fields like <code class="Inline">\vDesk\<?= Code::Class("DataProvider") ?>::<?= Code::Field("\$Null") ?></code>.
        </p>
        <pre><code><?= Conventions::NotRecommended ?>
<?= Code::If ?>(<?= Code::Variable("\$Value") ?> === <?= Code::Keyword("NULL") ?>) { }
</code></pre>
        <pre><code><?= Conventions::Recommended ?>
<?= Code::If ?>(<?= Code::Variable("\$Value") ?> === <?= Code::Keyword("null") ?>) { }
</code></pre>
    </section>
    <section id="UI">
        <h3>User Interface</h3>
        <p>
            This section describes visual guidelines and interaction rules of the client.
        </p>
        <p>
            As a general rule of thumb, it is recommended aiming to use as less as possible controls to achieve the desired functionality.
            <br>The primary focus of interaction lies on mouse control. Keyboard control may be implemented in scenarios where they provide an increase of value,
            <br>like navigating through grid elements, capturing certain keystrokes or deleting selected elements.
        </p>
    </section>
    <section id="CSS">
        <h4>CSS</h4>
        <p>
            CSS-classes must be named like the control's class they represent, located in a sub-directory located in the <code class="Inline">/vDesk/Client/Design</code>-directory, whose structure must represent the namespace of the control.
            <br>To avoid undefined behaviour and unnecessary intersections, selectors must be declared as explicit as possible.
            <br>This means if a control's class is named as <code class="Inline">.<?= Code::Class("ExampleControl") ?></code> and is a child of a DOM-node with the class <code class="Inline">.<?= Code::Class("ExampleParent") ?></code>,
            <br>the selector may limit the CSS declarations by using the <code class="Inline"><?= Code::Keyword(">") ?></code>-operator like
            <code class="Inline">.<?= Code::Class("Parent") ?> > .<?= Code::Class("ExampleControl") ?> { }</code>.
        </p>
        <p>
            If a classname intersects with a different one, consider using combined classnames for CSS-classes consisting of the bottom-most namespace and control's classname.
            <br>This means if for example the combination of <code class="Inline">.<?= Code::Class("CustomControl") ?></code><code class="Inline">.<?= Code::Class("Effect") ?></code> already exist, <code class="Inline">.<?= Code::Class("CustomControlEffect") ?></code> may be used instead.
        </p>
        <p>
            The client currently uses the box model and has been developed with the focus of running in all major desktop browsers,
            while the website already has been refactored to the CSS grid model.<br>
            Until it's not clear if it's easier to provide a single multi-device client or separate clients for desktop and mobile devices, the box model may still be used for new UI elements.
        </p>
        <p>
            The stack-order(z-index) range from 0 to 999 is reserved for controls of Packages in official releases.
        </p>
        <p>
            However if possible, it's recommended to use the CSS grid model at least within the workspace container of the client for modules which provide an UI.
        </p>
    </section>
    <section id="Colors">
        <h4>Colors</h4>
        <p>
            UI-controls have to use the colors of the table below according their purpose.
            If a control type is not listed in the color table, then it's recommend sticking to the next similar control type.
        </p>
        <table>
            <tr>
                <th>Control</th>
                <th>CSS class(es)</th>
                <th>CSS variable</th>
                <th>Runtime property</th>
                <th>Default color</th>
                <th></th>
            </tr>
            <tr>
                <td>Foreground areas, contrast color</td>
                <td><code class="Inline">.<?= Code::Class("Foreground") ?></code></td>
                <td><code class="Inline">--Foreground</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Foreground</code></td>
                <td>rgba(42, 176, 237, 1)</td>
                <td style="color: rgba(42, 176, 237, 1)">⬤</td>
            </tr>
            <tr>
                <td>Background areas</td>
                <td><code class="Inline">.<?= Code::Class("Background") ?></code></td>
                <td><code class="Inline">--Background</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Background</code></td>
                <td>rgba(255, 255, 255, 1)</td>
                <td style="background-color: black; color: rgba(255, 255, 255, 1)">⬤</td>
            </tr>
            <tr>
                <td>Light borders, disabled textboxes and windows out of focus</td>
                <td><code class="Inline">.<?= Code::Class("BorderLight") ?></code></td>
                <td><code class="Inline">--BorderLight</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.BorderLight</code></td>
                <td>rgba(153, 153, 153, 1)</td>
                <td style="color: rgba(153, 153, 153, 1)">⬤</td>
            </tr>
            <tr>
                <td>Dark borders of active and enabled controls and textboxes</td>
                <td><code class="Inline">.<?= Code::Class("BorderDark") ?></code></td>
                <td><code class="Inline">--BorderDark</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.BorderDark</code></td>
                <td>rgba(0, 0, 0, 1)</td>
                <td style="color: rgba(0, 0, 0, 1)">⬤</td>
            </tr>
            <tr>
                <td>Text in contrast areas</td>
                <td><code class="Inline">.<?= Code::Class("Font") ?></code> <code class="Inline">.<?= Code::Class("Light") ?></code></td>
                <td><code class="Inline">--FontLight</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.FontLight</code></td>
                <td>rgba(255, 255, 255, 1)</td>
                <td style="background-color: black; color: rgba(255, 255, 255, 1)">⬤</td>
            </tr>
            <tr>
                <td>Text in active and enabled controls</td>
                <td><code class="Inline">.<?= Code::Class("Font") ?></code> <code class="Inline">.<?= Code::Class("Dark") ?></code></td>
                <td><code class="Inline">--FontDark</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.FontDark</code></td>
                <td>rgba(0, 0, 0, 1)</td>
                <td style="color: rgba(0, 0, 0, 1)">⬤</td>
            </tr>
            <tr>
                <td>Text in disabled controls or windows out of focus</td>
                <td><code class="Inline">.<?= Code::Class("Font") ?></code> <code class="Inline">.<?= Code::Class("Disabled") ?></code></td>
                <td><code class="Inline">--FontDisabled</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.FontDisabled</code></td>
                <td>rgba(153, 153, 153, 1)</td>
                <td style="color: rgba(153, 153, 153, 1)">⬤</td>
            </tr>
            <tr>
                <td>Selected button controls and list elements</td>
                <td><code class="Inline">.<?= Code::Class("Control") ?></code> <code class="Inline">.<?= Code::Class("Selected") ?></code></td>
                <td><code class="Inline">--ControlSelected</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Control.Selected</code></td>
                <td>rgba(255, 207, 50, 1)</td>
                <td style="color: rgba(255, 207, 50, 1)">⬤</td>
            </tr>
            <tr>
                <td>Hover effect for button controls and list elements</td>
                <td><code class="Inline">.<?= Code::Class("Control") ?></code> <code class="Inline">.<?= Code::Class("Hover") ?></code></td>
                <td><code class="Inline">--ControlHover</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Control.Hover</code></td>
                <td>rgba(42, 176, 237, 1)</td>
                <td style="color: rgba(42, 176, 237, 1)">⬤</td>
            </tr>
            <tr>
                <td>Pressed button controls and list elements</td>
                <td><code class="Inline">.<?= Code::Class("Control") ?></code> <code class="Inline">.<?= Code::Class("Press") ?></code></td>
                <td><code class="Inline">--ControlPress</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Control.Press</code></td>
                <td>rgba(70, 140, 207, 1)</td>
                <td style="color: rgba(70, 140, 207, 1)">⬤</td>
            </tr>
            <tr>
                <td>Selected buttons</td>
                <td><code class="Inline">.<?= Code::Class("Button") ?></code> <code class="Inline">.<?= Code::Class("Selected") ?></code></td>
                <td><code class="Inline">--ButtonSelected</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Button.Selected</code></td>
                <td>rgba(170,170,170, 1)</td>
                <td style="color: rgba(170,170,170, 1)">⬤</td>
            </tr>
            <tr>
                <td>Hover effect for buttons</td>
                <td><code class="Inline">.<?= Code::Class("Button") ?></code> <code class="Inline">.<?= Code::Class("Hover") ?></code></td>
                <td><code class="Inline">--ButtonHover</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Button.Hover</code></td>
                <td>rgba(153, 153, 153, 1)</td>
                <td style="color: rgba(153, 153, 153, 1)">⬤</td>
            </tr>
            <tr>
                <td>Pressed buttons</td>
                <td><code class="Inline">.<?= Code::Class("Button") ?></code> <code class="Inline">.<?= Code::Class("Press") ?></code></td>
                <td><code class="Inline">--ButtonPress</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Button.Press</code></td>
                <td>rgba(119, 119, 119, 1)</td>
                <td style="color: rgba(119, 119, 119, 1)">⬤</td>
            </tr>
            <tr>
                <td>Background areas of buttons</td>
                <td><code class="Inline">.<?= Code::Class("Button") ?></code> <code class="Inline">.<?= Code::Class("Background") ?></code></td>
                <td><code class="Inline">--ButtonPress</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.Button.Background</code></td>
                <td>rgba(219,219,219, 1)</td>
                <td style="color: rgba(219,219,219, 1)">⬤</td>
            </tr>
            <tr>
                <td>Borders of selected textboxes</td>
                <td><code class="Inline">.<?= Code::Class("TextBox") ?></code> <code class="Inline">.<?= Code::Class("Selected") ?></code></td>
                <td><code class="Inline">--TextBoxSelected</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.TextBox.Selected</code></td>
                <td>rgba(255, 207, 50, 1)</td>
                <td style="color: rgba(255, 207, 50, 1)">⬤</td>
            </tr>
            <tr>
                <td>Borders of textboxes with invalid input</td>
                <td><code class="Inline">.<?= Code::Class("TextBox") ?></code> <code class="Inline">.<?= Code::Class("Error") ?></code></td>
                <td><code class="Inline">--TextBoxError</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.TextBox.Error</code></td>
                <td>rgba(255, 51, 0, 1)</td>
                <td style="color: rgba(255, 51, 0, 1)">⬤</td>
            </tr>
            <tr>
                <td>Borders of disabled and inactive textboxes</td>
                <td><code class="Inline">.<?= Code::Class("TextBox") ?></code> <code class="Inline">.<?= Code::Class("Disabled") ?></code></td>
                <td><code class="Inline">--TextBoxDisabled</code></td>
                <td><code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Colors") ?>.TextBox.Disabled</code></td>
                <td>rgba(153, 153, 153, 1)</td>
                <td style="color: rgba(153, 153, 153, 1)">⬤</td>
            </tr>
        </table>
    </section>
    <section id="Icons">
        <h4>Icons</h4>
        <p>
            Until the system is capable of handling and <a href="<?= Functions::URL("vDesk", "Page", "Roadmap#Colors") ?>">coloring SVG-images</a>, icons have to meet the following
            conditions:
        </p>
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
    </section>
    <section id="Database">
        <h3>Database</h3>
        <p>
            This section describes the access on database servers, SQL-code requirements and naming conventions.
        </p>
    </section>
    <section id="SQL">
        <h4>SQL</h4>
        <p>
            Although the "Structured Query Language" already provides an abstract way of communicating with database servers, there still exist some inconsistencies between certain
            RDBMS.<br>
            These differences occur mostly while working with schemas, tables, column definitions and data types and are usually minor implementation details.<br>
            So it still may happen that for example database "A" only supports the "MODIFY"-keyword, while database "B" only supports the "ALTER"-keyword while database "C" supports both,
            and so on..
        </p>
        <p>
            To address common differences between several RDBMs, vDesk's DataProvider ships with an <a href="<?= Functions::URL("Documentation", "Category", "Server", "Topic", "Database#Expressions") ?>">Expression</a>-library, that allows developers to "express" the operation they want
            to perform against the database server in a fluent interface that translates the action into a query compatible to the current configured database system.
            <br>The expression library covers most oft the usual CRUD and DB/Table-manipulation functions that SQL defines.
        </p>
        <p>
            If you're currently missing a feature and as long as your code only consists of standard conform SQL-statements,<br>
            you may pass any plain SQL-strings via the <code class="Inline"><?= Code::Class("DataProvider") ?>::<?= Code::Function("Execute") ?>()</code>-method directly to the underlying
            driver (like <a target="_blank" href="https://www.php.net/manual/de/mysqli.real-query.php">mysqli::real_query()</a>, <a target="_blank"
                                                                                                                                    href="https://www.php.net/manual/de/function.pg-query.php">pg_query()</a>
            or <a target="_blank" href="https://www.php.net/manual/de/pdo.query.php">PDO::query()</a>.<br>
            However, consider opening an issue on <a href="https://www.github.com/vDesk-Cloud">Github</a> or submitting a pull request.
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
    </section>
    <section id="DatabaseNamingConventions">
        <h4>Naming conventions</h4>
        <p>
            Databases, schemas and tables have to be written in "PascalCase"-notation, while schemas (databases in case of MySQL) have to be named like the Package the schema belongs.
            <br>Tables must be named with a pluralized version of the model they represent, in case the schema name implies it's purpose, table names may be written as an adjective.
        </p>
        <p>
            Tables must be strictly prefixed in plain SQL statements and Expressions with their parent schema or database while fields only may be prefixed if the statement references more than one table.
        </p>
    </section>
    <section id="Models">
        <h4>Models</h4>
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
<?= Code::ClassDeclaration ?> <?= Code::Class("Model") ?> <?= Code::Implements ?> \vDesk\Data\<?= Code::Class("IModel") ?> {
    
    <?= Code::Use ?> \vDesk\Struct\<?= Code::Class("Properties") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Private ?> ?<?= Code::Class("Dependency") ?> <?= Code::Field("\$Dependency") ?> = <?= Code::Null ?><?= Code::Delimiter ?>
    
    
    <?= Code::Private ?> ?<?= Code::Keyword("int") ?> <?= Code::Field("\$DependencyValue") ?> = <?= Code::Null ?><?= Code::Delimiter ?>
    
    
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
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(<?= Code::Class("Dependency") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("Dependency") ?> = <?= Code::Variable("\$Value") ?>
          
            ],
            <?= Code::String("\"Manual\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?<?= Code::Keyword("int") ?> => <?= Code::Return ?> <?= Code::Variable("\$this") ?>-><?= Code::Field("DependencyValue") ?> ??= (<?= Code::Keyword("int") ?>)<?= Code::Class("Expression") ?>::<?= Code::Function("Select") ?>(<?= Code::String("\"DependencyValue\"") ?>)
                                                                                       -><?= Code::Function("From") ?>(<?= Code::String("\"Table\"") ?>)
                                                                                       -><?= Code::Function("Where") ?>([<?= Code::String("\"ID\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>])(),
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(<?= Code::Keyword("int") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("DependencyValue") ?> = <?= Code::Variable("\$Value") ?>
          
            ]
        ])<?= Code::Delimiter ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ID") ?>(): ?<?= Code::Keyword("int") ?> {
        <?= Code::Return ?> <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?><?= Code::Delimiter ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Fill") ?>(): <?= Code::Class("Model") ?> {
        <?= Code::Comment("//Fill model with database record..") ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Save") ?>(): <?= Code::Keyword("void") ?> {
        <?= Code::Comment("//Create or update database record..") ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Delete") ?>(): <?= Code::Keyword("void") ?> {
        <?= Code::Comment("//Delete associated database record..") ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("FromDataView") ?>(<?= Code::Keyword("array") ?> <?= Code::Variable("\$DataView") ?>): <?= Code::Class("Model") ?> {
        <?= Code::Return ?> <?= Code::New ?> <?= Code::Static ?>(
            <?= Code::Variable("\$DataView") ?>[<?= Code::String("\"ID\"") ?>] ?? <?= Code::Null ?>,
            <?= Code::Class("Dependency") ?>::<?= Code::Function("FromDataView") ?>(<?= Code::Variable("\$DataView") ?>),
            ...
        )<?= Code::Delimiter ?>
        
    }
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ToDataView") ?>(): <?= Code::Keyword("array") ?> {
        <?= Code::Return ?> [
            <?= Code::String("\"ID\"") ?>         => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>,
            <?= Code::String("\"Dependency\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Dependency") ?>-><?= Code::Function("ToDataView") ?>(),
            ...
        ]<?= Code::Delimiter ?>
        
    }
    
}
</code></pre>
    </section>
</article>