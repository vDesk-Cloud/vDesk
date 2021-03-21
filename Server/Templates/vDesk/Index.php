<?php

use vDesk\Documentation\Code;
use vDesk\Pages\Functions;
use vDesk\Struct\Type;

?>
<section id="vDesk" class="SlideIn Bottom">
    <h2>v<span style="color: #2AB0ED">D</span>esk - The virtual <span style="color: #2AB0ED">D</span>esktop of the web</h2>
    <img src="<?= Functions::Image("vDesk/vDesk.png") ?>" alt="vDesk">
    <img src="<?= Functions::Image("Packages/ArchiveOverview.png") ?>" alt="Archive">
    <img src="<?= Functions::Image("Packages/CalendarMonthView.png") ?>" alt="Calendar">
    <aside class="Box SlideIn Left">
        <p>
            v<span style="color: #2AB0ED">D</span>esk is a self hosted open source personal cloud<br>
            that has been built with the focus of combining a simple yet intuitive client<br>
            with a feature-rich and customizable server environment.
        </p>
        <p>
            <a class="Button" href="<?= Functions::URL("vDesk", "Page", "GetvDesk") ?>">Get vDesk</a>
        </p>
    </aside>
    <aside class="Box SlideIn Bottom">
        <p>
            Access and manage your files from anywhere, organize your dates and stay in contact with others. <br>
            v<span style="color: #2AB0ED">D</span>esk comes with a predefined feature rich set of packages.
        </p>
        <p>
            <a class="Button" href="#Features">🡳 Checkout features 🡳</a>
        </p>
    </aside>
</section>
<hr>
<section id="Features" class="SlideIn">
    <div id="FeatureSlideShow" class="SlideShow">
        <div class="Slide Preview" id="Archive">
            <h2>Archive</h2>
            <aside class="Box" id="FileShare">
                <h4>Access your files from anywhere</h4>
                <p>AccessControlList-based sharing of files and folders with users and groups</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Archive") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/ArchiveOverview.png") ?>" alt="Archive">
        </div>
        <div class="Slide Preview" id="Calendar">
            <h2>Calendar</h2>
            <aside class="Box">
                <h4>Keep track of your business</h4>
                <p>AccessControlList-based event planning with users and groups</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Calendar") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/CalendarMonthView.png") ?>" alt="Calendar">
        </div>
        <div class="Slide Preview" id="Contacts">
            <h2>Contacts</h2>
            <aside class="Box">
                <h4>Stay in contact with friends and business partners</h4>
                <p>AccessControlList-based contact-management of private- and company contacts</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Contacts") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Contacts.png") ?>" alt="Contacts">
        </div>
        <div class="Slide Preview" id="Messenger">
            <h2>Messenger</h2>
            <aside class="Box">
                <h4>Exchange with others</h4>
                <p>Have private conversations or discuss in groups</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Messenger") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Messenger.png") ?>" alt="Messenger">
        </div>
        <div class="Slide Preview" id="Pinboard">
            <h2>Pinboard</h2>
            <aside class="Box">
                <h4>Organize yourself</h4>
                <p>Create custom notes and attach frequently used files and folders</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Pinboard") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Pinboard.png") ?>" alt="Pinboard">
        </div>
        <div class="Slide Preview" id="Search">
            <h2>Search</h2>
            <aside class="Box">
                <h4>Keep the overview</h4>
                <p>Quickly find files, folders, calendar-events or contacts</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Search") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Search.png") ?>" alt="Search">
        </div>
        <div class="Slide Preview" id="Colors">
            <h2>Colors</h2>
            <aside class="Box">
                <h4>Life is colorful</h4>
                <p>Customize the <i>look&feel</i> of v<span style="color: #2AB0ED">D</span>esk like your taste</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Colors") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Colors.png") ?>" alt="Colors">
        </div>
    </div>
</section>
<hr>
<section id="Technology" class="SlideIn Right Paused">
    <div>
        <aside id="Platforms" class="Box SlideIn Right Paused" style="animation-delay: 1s; float: right">
            <h2><img src="<?= Functions::Image("vDesk", "Platform.png") ?>"> Platform independent</h2>
            <p>
                v<span style="color: #2AB0ED">D</span>esk is designed to run on any operating system <br>capable of running PHP 8 with minimum permissive settings.
            </p>
        </aside>
        <div style="clear: right"></div>
    </div>
    <div class="Browsers" style="text-align: center">
        <img class="SlideIn Left Paused" style="animation-delay: 0.9s" alt="Microsoft Edge" src="<?= Functions::Image("vDesk", "Platform", "Edge.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 0.5s" alt="Mozilla Firefox" src="<?= Functions::Image("vDesk", "Platform", "Firefox.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 0.7s" alt="Google Chrome" src="<?= Functions::Image("vDesk", "Platform", "Chrome.png") ?>">
    </div>
    <hr>
    <div class="Technologies" style="text-align: center">
        <img class="SlideIn Left Paused" style="animation-delay: 1.1s" alt="PHP 7" src="<?= Functions::Image("vDesk", "Platform", "PHP.png") ?>">
        <img class="SlideIn Left Paused" style="animation-delay: 0.9s" alt="JavaScript" src="<?= Functions::Image("vDesk", "Platform", "JS.png") ?>">
        <img class="SlideIn Left Paused" style="animation-delay: 0.5s" alt="CSS3" src="<?= Functions::Image("vDesk", "Platform", "CSS3.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 0.7s" alt="MySQL" src="<?= Functions::Image("vDesk", "Platform", "MySQL.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 1s" alt="HTML5" src="<?= Functions::Image("vDesk", "Platform", "HTML.png") ?>">
    </div>
    <hr>
    <div class="OperatingSystems" style="text-align: center">
        <img class="SlideIn Left Paused" style="animation-delay: 0.7s" alt="Raspberry Pi" src="<?= Functions::Image("vDesk", "Platform", "RaspberryPi.png") ?>">
        <img class="SlideIn Left Paused" style="animation-delay: 0.5s" alt="Linux" src="<?= Functions::Image("vDesk", "Platform", "Linux.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 0.9s" alt="Windows" src="<?= Functions::Image("vDesk", "Platform", "Windows.png") ?>">
    </div>
    <div style="position: relative">
        <aside id="Resources" class="Box SlideIn Left Paused" style="animation-delay: 1.1s">
            <h2><img src="<?= Functions::Image("vDesk", "Performance.png") ?>"> Small resource footprint</h2>
            <p>
                vDesk is an entirely handcrafted project that comes without any dependencies to third-party libraries or frameworks.
            </p>
            <p>
                Every single line of code is handcrafted, which implies a small footprint of resource usage<br> (It even runs on a Raspberry Pi 2B without any problems).
            </p>
        </aside>
    </div>
</section>
<hr>
<section id="Customizable" class="SlideIn Left Paused">
    <img src="<?= Functions::Image("vDesk", "Packages.png") ?>">
    <aside id="PackageSystem" class="SlideIn Right Box Paused" style="animation-delay: 0.5s">
        <h2>Customizable <img src="<?= Functions::Image("vDesk", "Customizable.png") ?>"></h2>
        <p>
            v<span style="color: #2AB0ED">D</span>esk ships with a powerful yet simple package system that allows running installations to be customized on the fly.<br>
            It's entirely your decision which features the system provides!<br>
            (Even this website is a <a href="<?= Functions::URL("vDesk", "Page", "Packages#Homepage") ?>">package</a> that is based on another <a
                    href="<?= Functions::URL("vDesk", "Page", "Packages#Pages") ?>">package</a> that implements a simple MVC-framework.)
        </p>
    </aside>
    <aside id="Packages" class="SlideIn Right Box Paused" style="animation-delay: 0.75s">
        <h2><img src="<?= Functions::Image("vDesk", "Package.png") ?>"> Packages</h2>
        <p>The standard release of v<span style="color: #2AB0ED">D</span>esk contains a preselected collection of feature rich packages</p>
        <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages") ?>">Explore packages</a>
    </aside>
    <aside id="OpenSource" class="SlideIn Right Box Paused" style="animation-delay: 1s">
        <h2><img src="<?= Functions::Image("vDesk", "Code.png") ?>"> Open source</h2>
        <p>
            v<span style="color: #2AB0ED">D</span>esk is licensed under the Microsoft Public License which allows package-authors to create custom setups bundled with their own
            licensed packages.<br>
            <a class="Button" href="<?= Functions::URL("Documentation", "Page", "Packages") ?>">Learn more about custom packages</a>
        </p>
    </aside>
</section>
<hr>
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
