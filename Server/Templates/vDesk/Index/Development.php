<?php
declare(strict_types=1);

use vDesk\Pages\Functions;
use vDesk\Documentation\Code;

?>
<section id="Development" class="SlideIn">
    <div class="SlideShow" id="CodeSlideShow">
        <div class="Slide Code" id="Interfaces">
            <aside class="Box">
                <h2>Modular application design</h2>
                <p>
                    v<span style="color: #2AB0ED">D</span>esk aims to provide a high level of modularity through APIs to establish a developer friendly environment.<br>
                    <a class="Button" href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", "ModulesCommands") ?>">Learn more about how vDesk works</a>
                </p>
            </aside>
            <pre><code><?= Code\Language::JS ?>
                    <?= Code::BlockComment("/**
 * Asynchronous custom console-commands
 */") ?>
                    
                    <?= Code::Variable("vDesk") ?>.<?= Code::Class("Console") ?>.Commands.<?= Code::Function("Greet") ?> = <?= Code::Async ?> <?= Code::Function ?> (Console, Arguments = {}) {
    
    Console.<?= Code::Function("Write") ?>(<?= Code::String("`Greetings, ") ?>${Arguments?.<?= Code::Field("Name") ?> ?? <?= Code::Variable("vDesk") ?>.User.<?= Code::Field("Name") ?>}<?= Code::String("!`") ?>)<?= Code::Delimiter ?>
                    
                    
                    <?= Code::Switch ?>(<?= Code::Await ?> Console.<?= Code::Function("Read") ?>(<?= Code::String("\"How are you today? \"") ?>)) {
        <?= Code::Case ?> <?= Code::String("\"good\"") ?>:
        <?= Code::Case ?> <?= Code::String("\"fine\"") ?>:
            Console.<?= Code::Function("Write") ?>(<?= Code::String("\"Nice to hear!\"") ?>)<?= Code::Delimiter ?>
                    
                    <?= Code::Break ?><?= Code::Delimiter ?>
                    
                    <?= Code::Case ?> <?= Code::String("\"bad\"") ?>:
            Console.<?= Code::Function("Write") ?>(<?= Code::String("\"Sad to hear; but anyway, have a nice day!\"") ?>)<?= Code::Delimiter ?>
        
    }
    
}<?= Code::Delimiter ?>
                    
                    
                    <?= Code::BlockComment("/**
 * Really pointless command... (") ?><a style="cursor: pointer"
                                        onclick="window.addEventListener('click', Event => Event.target.parentNode.removeChild(Event.target), true);"><?= Code::BlockComment("trust me!") ?></a><?= Code::BlockComment(")
 */") ?>
                    
                    <?= Code::Variable("vDesk") ?>.<?= Code::Class("Console") ?>.Commands.<?= Code::Function("PointlessCommand") ?> = <?= Code::Async ?> <?= Code::Function ?> (Console, Arguments = {}) {
    <?= Code::Variable("window") ?>.<?= Code::Function("addEventListener") ?>(
        <?= Code::String("\"click\"") ?>,
        Event => Event.<?= Code::Field("target") ?>.<?= Code::Field("parentNode") ?>.<?= Code::Function("removeChild") ?>(Event.<?= Code::Field("target") ?>),
        <?= Code::Bool("true") ?>
        
    )<?= Code::Delimiter ?>
    
}<?= Code::Delimiter ?>
</code></pre>
            <img src="<?= Functions::Image("vDesk", "ConsoleCommands.png") ?>" alt="ConsoleCommands">
        </div>
        <div class="Slide Code" id="Expressions">
            <aside class="Box">
                <h2>Expressive database access</h2>
                <p>
                    Instead of relying on a complicated ORM, vDesk brings it's own abstract way of communicating with databases that allows developers to just "express" in an
                    SQL-esque manner what they really want to do.<br>
                    <a class="Button" href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", "Expressions") ?>">Learn more about Expressions</a>
                </p>
            </aside>
            <pre><code><?= Code\Language::PHP ?>
                    <?= Code::Variable("\$Permissions") ?> = \vDesk\DataProvider\<?= Code::Class("Expression") ?>
    
    ::<?= Code::Function("Select") ?>(
        [<?= Code::Class("λ") ?>::<?= Code::Function("Max") ?>(<?= Code::String("\"Read\"") ?>), <?= Code::String("\"Read\"") ?>],
        [<?= Code::Class("λ") ?>::<?= Code::Function("Max") ?>(<?= Code::String("\"Write\"") ?>), <?= Code::String("\"Write\"") ?>],
        [<?= Code::Class("λ") ?>::<?= Code::Function("Max") ?>(<?= Code::String("\"Delete\"") ?>), <?= Code::String("\"Delete\"") ?>]
    )
    -><?= Code::Function("From") ?>(
        <?= Code::Class("Expression") ?>
            
            ::<?= Code::Function("Select") ?>(
                <?= Code::String("\"Read\"") ?>,
                <?= Code::String("\"Write\"") ?>,
                <?= Code::String("\"Delete\"") ?>
            
            )
            -><?= Code::Function("From") ?>(<?= Code::String("\"Security.AccessControlListEntries\"") ?>)
            -><?= Code::Function("InnerJoin") ?>(<?= Code::String("\"Security.GroupMemberships\"") ?>)
            -><?= Code::Function("On") ?>([<?= Code::String("\"AccessControlListEntries.Group\"") ?> => <?= Code::String("\"GroupMemberships.Group\"") ?>])
            -><?= Code::Function("Where") ?>([
                <?= Code::String("\"AccessControlListEntries.AccessControlList\"") ?> => <?= Code::Variable("\$ID") ?>,
                <?= Code::String("\"GroupMemberships.User\"") ?>                      => <?= Code::Variable("\$User") ?> ?? \vDesk::<?= Code::Variable("\$User") ?>
            
            ])
            -><?= Code::Function("Union") ?>(
                <?= Code::Class("Expression") ?>
            
                    ::<?= Code::Function("Select") ?>(
                        <?= Code::String("\"Read\"") ?>,
                        <?= Code::String("\"Write\"") ?>,
                        <?= Code::String("\"Delete\"") ?>
            
                    )
                    -><?= Code::Function("From") ?>([<?= Code::String("\"Security.AccessControlListEntries\"") ?> => <?= Code::String("\"Entries\"") ?>])
                    -><?= Code::Function("Where") ?>([
                        <?= Code::String("\"Entries.AccessControlList\"") ?> => <?= Code::Variable("\$ID") ?>,
                        <?= Code::String("\"Entries.User\"") ?>              => <?= Code::Variable("\$User") ?> ?? \vDesk::<?= Code::Variable("\$User") ?>
            
                    ])
            ),
         <?= Code::String("\"Permissions\"") ?>
         
    )
    -><?= Code::Function("Execute") ?>()
    -><?= Code::Function("ToMap") ?>()<?= Code::Delimiter ?>
</code></pre>
            <pre><code><?= Code\Language::PHP ?>
                    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Save") ?>(): <?= Code::Void ?> {
    <?= Code::If ?>(<?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?> !== <?= Code::Null ?>) {
        <?= Code::Class("Expression") ?>::<?= Code::Function("Update") ?>(<?= Code::String("\"Archive.Elements\"") ?>)
                  -><?= Code::Function("SetIf") ?>([
                      <?= Code::String("\"Name\"") ?>      => [<?= Code::Variable("\$this") ?>-><?= Code::Field("NameChanged") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Name") ?>],
                      <?= Code::String("\"File\"") ?>      => [<?= Code::Variable("\$this") ?>-><?= Code::Field("FileChanged") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("File") ?>],
                      <?= Code::String("\"Thumbnail\"") ?> => [<?= Code::Variable("\$this") ?>-><?= Code::Field("ThumbnailChanged") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Thumbnail") ?>]
                  ])
                  -><?= Code::Function("Where") ?>([<?= Code::String("\"ID\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>])
                  -><?= Code::Function("Execute") ?>()<?= Code::Delimiter ?>
        
    } <?= Code::Else ?> {
        <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?> = <?= Code::Class("Expression") ?>::<?= Code::Function("Insert") ?>()
                              -><?= Code::Function("Into") ?>(<?= Code::String("\"Archive.Elements\"") ?>)
                              -><?= Code::Function("Values") ?>([
                                  <?= Code::String("\"ID\"") ?>        => <?= Code::Null ?>,
                                  <?= Code::String("\"Name\"") ?>      => <?= Code::Variable("\$this") ?>-><?= Code::Field("Name") ?>,
                                  <?= Code::String("\"File\"") ?>      => <?= Code::Variable("\$this") ?>-><?= Code::Field("File") ?>,
                                  <?= Code::String("\"Thumbnail\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Thumbnail") ?>
        
                              ])
                              -><?= Code::Function("ID") ?>()<?= Code::Delimiter ?>
        
    }
}<?= Code::Delimiter ?>
    </code></pre>
        </div>
        <div class="Slide Code" id="RapidDevelopment">
            <aside class="Box">
                <h2>Rapid development</h2>
                <p>
                    vDesk ships a predefined set of features for common tasks like string manipulations, accessing files, communicating with databases or working with collections
                    for example.<br>
                    <a class="Button" href="<?= Functions::URL("Documentation", "Index") ?>">Checkout the docs & tutorials</a>
                </p>
            </aside>
            <div>
                <pre><code><?= Code\Language::PHP ?>
                        <?= Code::Use ?> \vDesk\Struct\<?= Code::Class("Properties") ?><?= Code::Delimiter ?>
        
        
                        <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?>() {
    <?= Code::Comment("//Type-safe properties.") ?>
        
                        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperties") ?>([
        <?= Code::String("\"Property\"") ?> => [
            \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?string => <?= Code::Variable("\$this") ?>-><?= Code::Field("Property") ?>,
            \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(string <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("Property") ?> = <?= Code::Variable("\$Value") ?>
      
        ],
        ...
    ])<?= Code::Delimiter ?>
    
}<?= Code::Delimiter ?>
</code></pre>
            </div>
            <div>
                <div style="width: calc( 50% - 10px); padding-right: 10px; float: left">
                
                <pre><code><?= Code\Language::JS ?>
                        <?= Code::Constant ?> <?= Code::Const("ColorPicker") ?> = <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("`") ?>${<?= Code::Variable("vDesk") ?>.<?= Code::Class("Locale") ?>.<?= Code::Field("Colors") ?>.<?= Code::Field("Color") ?>}<?= Code::String(":`") ?>,
    <?= Code::String("\"Pick a color\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Color") ?>,
    <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("Color") ?>.<?= Code::Function("FromRGBAString") ?>(<?= Code::String("\"rgba(42, 176, 237, 1.0)\"") ?>)
)<?= Code::Delimiter ?>
        
        
                        <?= Code::Constant ?> <?= Code::Const("Text") ?> = <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Text:\"") ?>,
    <?= Code::Null ?>,
    <?= Code::Variable("Type") ?>.<?= Code::Field("String") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Expression") ?>: <?= Code::String("\"\\S\"") ?>,  <?= Code::Variable("Max") ?>: <?= Code::Int("255") ?>}
)<?= Code::Delimiter ?>
</code></pre>
                </div>
                <div style="width: calc( 50% - 10px); padding-left: 10px; float: right">
                    <pre><code><?= Code\Language::PHP ?>
                            <?= Code::Use ?> \vDesk\Struct\Collections\<?= Code::Class("Collection") ?><?= Code::Delimiter ?>
        
                            <?= Code::Use ?> \vDesk\Struct\Collections\Observable\<?= Code::Class("Dictionary") ?><?= Code::Delimiter ?>
        
        
                            <?= Code::Comment("//Prints the number \"21\"") ?>
        
                            <?= Code::Keyword("print") ?> (<?= Code::New ?> <?= Code::Class("Collection") ?>([<?= Code::String("\"1\"") ?>, <?= Code::String("\"3\"") ?>, <?= Code::String("\"5\"") ?>, <?= Code::String("\"7\"") ?>, <?= Code::String("\"9\"") ?>]))
    -><?= Code::Function("Map") ?>(<?= Code::Keyword("fn") ?>(string <?= Code::Variable("\$Value") ?>): int => (<?= Code::Keyword("int") ?>)<?= Code::Variable("\$Value") ?>)
    -><?= Code::Function("Filter") ?>(<?= Code::Keyword("fn") ?>(string <?= Code::Variable("\$Value") ?>): bool => <?= Code::Variable("\$Value") ?> >= <?= Code::Int("5") ?>)
    -><?= Code::Function("Reduce") ?>(<?= Code::Keyword("fn") ?>(int <?= Code::Variable("\$Accumulated") ?>, int <?= Code::Variable("\$Value") ?>): int => <?= Code::Variable("\$Accumulated") ?> + <?= Code::Variable("\$Value") ?>, <?= Code::Int("0") ?>)<?= Code::Delimiter ?>
        
        
                            <?= Code::Comment("//Prints the number \"4\"") ?>
        
                            <?= Code::Variable("\$Dictionary") ?> = <?= Code::New ?> <?= Code::Class("Dictionary") ?>([<?= Code::String("\"one\"") ?> => <?= Code::Int("1") ?>, <?= Code::String("\"two\"") ?> => <?= Code::Int("2") ?>, <?= Code::String("\"three\"") ?> => <?= Code::Int("3") ?>])<?= Code::Delimiter ?>
        
                            <?= Code::Variable("\$Dictionary") ?>-><?= Code::Field("OnAdd") ?>[] = <?= Code::Keyword("fn") ?>(int <?= Code::Variable("\$Value") ?>) => <?= Code::Keyword("echo") ?> <?= Code::Variable("\$Value") ?><?= Code::Delimiter ?>
        
                            <?= Code::Variable("\$Dictionary") ?>-><?= Code::Function("Add") ?>(<?= Code::String("\"four\"") ?>, <?= Code::Int("4") ?>)<?= Code::Delimiter ?>
</code></pre>
                </div>
            </div>
            <div style="clear: both"></div>
            <div>
            <pre><code><?= Code\Language::PHP ?>
                    <?= Code::Use ?> \vDesk\IO\<?= Code::Class("FileStream") ?><?= Code::Delimiter ?>
        
                    <?= Code::Use ?> \vDesk\IO\Stream\<?= Code::Class("Mode") ?><?= Code::Delimiter ?>
        
        
                    <?= Code::Variable("\$File") ?> = <?= Code::New ?> <?= Code::Class("FileStream") ?>(<?= Code::String("\"file.txt\"") ?>, <?= Code::Class("Mode") ?>::<?= Code::Const("Create") ?> | <?= Code::Class("Mode") ?>::<?= Code::Const("Binary") ?>)<?= Code::Delimiter ?>
        
                    <?= Code::Variable("\$File") ?>-><?= Code::Function("Write") ?>(<?= Code::String("\"Hello world!\"") ?>)<?= Code::Delimiter ?>
        
                    <?= Code::Variable("\$File") ?>-><?= Code::Function("Close") ?>()<?= Code::Delimiter ?>
</code></pre>
            </div>
        </div>
    </div>
</section>
