<?php
use vDesk\Pages\Functions;
use vDesk\Documentation\Code;
?>
<article class="Pages">
    <header>
        <h2>Pages</h2>
        <p>
            This document describes how to use the Pages MVC-framework.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Routing">Routing</a></li>
            <li><a href="#Pages">Pages (Models)</a></li>
            <li><a href="#Templates">Templates (Views)</a></li>
            <li><a href="#Modules">Modules (Controllers)</a></li>
            <li><a href="#Functions">Functions</a></li>
        </ul>
    </header>
    <section id="Routing">
        <h3>Routing</h3>
        <p>
            Application flow:
            Pages first checks, if the request contains ordinary CGI-parameters like "?Module=ExampleModule&Command=ExampleCommand&param1=...
            If the request doesn't specify these values, or if they are not well formed, Pages tries to map the querystring on a configured route-definition.
            
        </p>
        <h4>CGI Parameters</h4>
        <p></p>
        <h4>Rest like</h4>
        <p>
            If no route matches the querystring, Pages tries to parse the querystring in a restful manner, checking for each segment if there may exist a matching Controller and action
            and treats every following segments as "key-value"-pairs/parameters if a Controller matches the querystring.
            If no matching Controller can be found, Pages tries to use a specified 'fallback'-route if the querystring omits any usable information.
        </p>
        <p>
            A call in the format of <code class="Inline">?/Blog/CreatePost/Topic/Hello world!/</code>
        </p>

        <pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("Blog") ?> <?= Code::Extends ?> \vDesk\Modules\<?= Code::Class("Module") ?> {

    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("CreatePost") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Topic") ?> = <?= Code::Null ?>, <?= Code::Keyword("string") ?> <?= Code::Variable("\$Text") ?> = <?= Code::Null ?>): <?= Code::Class("Page") ?> {
        \vDesk\Pages\<?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"Topic\"") ?>]
    }

}
        </code></pre>
    </section>
    <section id="Pages">
        <h3>Pages</h3>
        <p>
            Pages are the equivalent of models and define the stylesheet- and script-files to include, the templates to compose and content to display.<br>
            Depending on its purpose, a page can represent either an entire HTLM document, a fragment of a website, or just a single HTML tag.<br>
            The Pages framework provides a predefined <code class="Inline"><?= Code::Console("%InstallDir%/Server/Pages") ?></code>-directory stored in the <code class="Inline">\vDesk\Configuration\<?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"Pages\"") ?>]</code>-setting.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Namespace ?> Pages\MyPage<?= Code::Delimiter ?>


<?= Code::ClassDeclaration ?> <?= Code::Class("Page") ?> <?= Code::Extends ?> \vDesk\Pages\<?= Code::Class("Page") ?> {
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?>(
        ?<?= Code::Keyword("iterable") ?> <?= Code::Variable("\$Values") ?> = [
            <?= Code::String("\"Title\"") ?> => <?= Code::String("\"Hello world!\"") ?>,
            <?= Code::String("\"Pages\"") ?> => [<?= Code::String("\"Index\"") ?>, <?= Code::String("\"Blog\"") ?>, <?= Code::String("\"Imprint\"") ?>],
            <?= Code::String("\"Content\"") ?> => <?= Code::String("\"Lorem ipsum dolor sit amet...\"") ?>,
            <?= Code::String("\"Footer\"") ?> => <?= Code::String("\"Footnote\"") ?>
            
        ],
        ?<?= Code::Keyword("iterable") ?> <?= Code::Variable("\$Templates") ?> = [<?= Code::String("\"MyPage/Page\"") ?>],
        ?<?= Code::Keyword("iterable") ?> <?= Code::Variable("\$Stylesheets") ?> = [<?= Code::String("\"MyPage/Stylesheet\"") ?>],
        ?<?= Code::Keyword("iterable") ?> <?= Code::Variable("\$Scripts") ?> = [<?= Code::String("\"MyPage/Script\"") ?>]
    ): <?= Code::Void ?> {
        <?= Code::Parent ?>::<?= Code::Function("__construct") ?>(<?= Code::Variable("\$Values") ?>, <?= Code::Variable("\$Templates") ?>, <?= Code::Variable("\$Stylesheets") ?>, <?= Code::Variable("\$Scripts") ?>)<?= Code::Delimiter ?>
        
    }
}</code></pre>
    </section>
    <section id="Caching">
        <h4>Caching</h4>
        <p>
            The Pages framework provides
            Pages are the equivalent of models and define the stylesheet- and script-files to include, the templates to compose and content to display.<br>
            Depending on its purpose, a page can represent either an entire HTLM document, a fragment of a website, or just a single HTML tag.<br>
            The Pages framework provides a predefined <code class="Inline"><?= Code::Console("%InstallDir%/Server/Pages") ?></code>-directory stored in the <code class="Inline">\vDesk\Configuration\<?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"Pages\"") ?>]</code>-setting.
        </p>
    </section>
    <section id="Templates">
        <h3>Templates</h3>
        <p>
            Templates are "phtml"-files written in PHP's <a target="_blank" href="https://www.php.net/manual/en/control-structures.alternative-syntax.php">alternative syntax</a> describing the structure and look of its according (partial) Page.<br>
            The Pages framework provides a predefined <code class="Inline"><?= Code::Console("%InstallDir%/Server/Templates") ?></code>-directory stored in the <code class="Inline">\vDesk\Configuration\<?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"Templates\"") ?>]</code>-setting.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Keyword("&lt;?php use") ?> vDesk\Pages\<?= Code::Class("Functions") ?><?= Code::Keyword("; ?>") ?>

<?= Code::HTML("<!DOCTYPE html>") ?>

<?= Code::HTML("<html>") ?>

<?= Code::HTML("<head>") ?>

    <?= Code::HTML("<title>") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Title") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("</title>") ?>
    
    <?= Code::Keyword("&lt;?php foreach") ?>(<?= Code::Variable("\$Page") ?>-><?= Code::Field("Stylesheets") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Stylesheet") ?>): <?= Code::Keyword("?>") ?>
        
        <?= Code::HTML("<link rel=") ?><?= Code::String("\"stylesheet\"") ?> <?= Code::HTML("href=") ?><?= Code::String("\"") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Class("Functions") ?>::<?= Code::Function("Stylesheet") ?>(<?= Code::Variable("\$Stylesheet") ?>) <?= Code::Keyword("?>") ?><?= Code::String("\"") ?><?= Code::HTML("/>") ?>
        
    <?= Code::Keyword("&lt;?php endforeach; ?>") ?>
    
    <?= Code::Keyword("&lt;?php foreach") ?>(<?= Code::Variable("\$Page") ?>-><?= Code::Field("Scripts") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Script") ?>): <?= Code::Keyword("?>") ?>
        
        <?= Code::HTML("<script src=") ?><?= Code::String("\"") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Class("Functions") ?>::<?= Code::Function("Script") ?>(<?= Code::Variable("\$Script") ?>) <?= Code::Keyword("?>") ?><?= Code::String("\"") ?><?= Code::HTML("/>") ?>
        
    <?= Code::Keyword("&lt;?php endforeach; ?>") ?>
    
<?= Code::HTML("</head>") ?>

<?= Code::HTML("<body>") ?>
    
    <?= Code::HTML("<main>") ?>
    
        <?= Code::HTML("<header>") ?>
        
            <?= Code::HTML("<h1>") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Title") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("</h1>") ?>
            
            <?= Code::HTML("<nav>") ?>
        
                <?= Code::Keyword("&lt;?php foreach") ?>(<?= Code::Variable("\$Pages") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Page") ?>): <?= Code::Keyword("?>") ?>
        
                    <?= Code::HTML("<a") ?> <?= Code::HTML("href=") ?><?= Code::String("\"") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Class("Functions") ?>::<?= Code::Function("URL") ?>(<?= Code::String("\"MyPage\"") ?>, <?= Code::String("\"Page\"") ?>, <?= Code::Variable("\$Page") ?>) <?= Code::Keyword("?>") ?><?= Code::String("\"") ?><?= Code::HTML(">") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Page") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("</a>") ?>
        
                <?= Code::Keyword("&lt;?php endforeach; ?>") ?>
            
            <?= Code::HTML("</nav>") ?>
        
        <?= Code::HTML("</header>") ?>
        
        <?= Code::HTML("<article>") ?>
            
            <?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Content") ?> <?= Code::Keyword("?>") ?>
            
        <?= Code::HTML("</article>") ?>
        
        <?= Code::HTML("<footer>") ?>
            
            <?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Footer") ?> <?= Code::Keyword("?>") ?>
            
        <?= Code::HTML("</footer>") ?>
    
    <?= Code::HTML("</main>") ?>
    
<?= Code::HTML("</body>") ?>

<?= Code::HTML("</html>") ?>
</code></pre>
    </section>
    <section id="Composition">
        <h4>Composition</h4>
        <p>
            Templates can be easily reused via using the provided "Template"-function.<br>
            The template function accepts an optional array of key-value-pairs which get exported into the local symbol table.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::HTML("<article class=") ?><?= Code::String("\"Blogpost\"") ?><?= Code::HTML(">") ?>
    
    <?= Code::HTML("<header>") ?>
        
        <?= Code::HTML("<h2>") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Topic") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("<h2>") ?>
        
    <?= Code::HTML("</header>") ?>
    
    <?= Code::HTML("<section>") ?>
        
        <?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Text") ?> <?= Code::Keyword("?>") ?>
        
    <?= Code::HTML("</section>") ?>
    
    <?= Code::HTML("<footer>") ?>
        
        <?= Code::HTML("<p>") ?>
        
            Published at <?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Date") ?>-><?= Code::Function("format") ?>() <?= Code::Keyword("?>") ?>, by <?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Author") ?> <?= Code::Keyword("?>") ?>.
        <?= Code::HTML("</p>") ?>
        
    <?= Code::HTML("</footer>") ?>

<?= Code::HTML("</article>") ?>
        </code></pre>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Keyword("&lt;?php use") ?> vDesk\Pages\<?= Code::Class("Functions") ?><?= Code::Keyword("; ?>") ?>

<?= Code::HTML("<main class=") ?><?= Code::String("\"Blog\"") ?><?= Code::HTML(">") ?>

    <?= Code::HTML("<header>") ?>

        <?= Code::HTML("<h1>") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Title") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("<h1>") ?>

    <?= Code::HTML("</header>") ?>

    <?= Code::HTML("<section>") ?>
        
        <?= Code::Keyword("&lt;?php foreach") ?>(<?= Code::Variable("\$Posts") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Post") ?>): <?= Code::Keyword("?>") ?>
        
            <?= Code::Keyword("&lt;?=") ?> <?= Code::Class("Functions") ?>::<?= Code::Function("Template") ?>(
                    <?= Code::String("\"Blogpost\"") ?>,
                    [
                        <?= Code::String("\"Topic\"") ?>  => <?= Code::Variable("\$Post") ?>-><?= Code::Field("Topic") ?>,
                        <?= Code::String("\"Text\"") ?>   => <?= Code::Variable("\$Post") ?>-><?= Code::Field("Text") ?>,
                        <?= Code::String("\"Date\"") ?>   => <?= Code::New ?> \<?= Code::Class("DateTime") ?>(<?= Code::Variable("\$Post") ?>-><?= Code::Field("Date") ?>),
                        <?= Code::String("\"Author\"") ?> => <?= Code::Variable("\$Post") ?>-><?= Code::Field("Author") ?>
                
                    ]
                ) <?= Code::Keyword("?>") ?>
        
        <?= Code::Keyword("&lt;?php endforeach; ?>") ?>

    <?= Code::HTML("</section>") ?>

    <?= Code::HTML("<footer>") ?>

        <?= Code::HTML("<nav>") ?>
        
            <?= Code::HTML("<span>") ?>Page: <?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Page") ?>-><?= Code::Field("Page") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("</span>") ?>
        
        <?= Code::HTML("</nav>") ?>
        
    <?= Code::HTML("</footer>") ?>
    
<?= Code::HTML("</main>") ?>
        </code></pre>
    </section>
    <section id="Modules">
        <h3>Modules</h3>
        <p>
            Modules are the equivalent of Controllers and are responsible for validating requests, storing data and building Pages.<br>
            Instead of manually registering <a href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", "ModulesCommands#Commands") ?>">Commands</a>, the Pages framework provides a specialized <code class="Inline">\vDesk\Pages\<?= Code::Class("Request") ?></code>-interface that provides access to the submitted values of the querystring.
            Modules are located in the system's <code class="Inline"><?= Code::Console("%InstallDir%/Server/Modules") ?></code>-directory and can be accessed via the global <code class="Inline">\vDesk\<?= Code::Class("Modules") ?></code>-facade.

        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("MyPage") ?> <?= Code::Extends ?> \vDesk\Modules\<?= Code::Class("Module") ?> {

    <?= Code::BlockComment(<<<Comment
/**
     * Displays the index page of the website.
     */
Comment) ?>
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Index") ?>(): <?= Code::Class("Page") ?> {
        <?= Code::Return ?> <?= Code::New ?> \Pages\MyPage\<?= Code::Class("Index") ?>()<?= Code::Delimiter ?>
        
    }
    
    <?= Code::BlockComment(<<<Comment
/**
     * Displays the specified amount of blog posts older than the specified date.
     */
Comment) ?>
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Posts") ?>(\<?= Code::Class("DateTime") ?> <?= Code::Variable("\$Date") ?> = <?= Code::Null ?>, <?= Code::Keyword("int") ?> <?= Code::Variable("\$Amount") ?> = <?= Code::Null ?>): <?= Code::Class("Page") ?> {
        
        <?= Code::Return ?> <?= Code::New ?> \Pages\MyPage\<?= Code::Class("Blog") ?>([
            <?= Code::String("\"Title\"") ?> => <?= Code::String("\"My cool blog\"") ?>,
            <?= Code::String("\"Posts\"") ?> => <?= Code::Class("Expression") ?>::<?= Code::Function("Select") ?>(<?= Code::String("\"*\"") ?>)
                             -><?= Code::Function("From") ?>(<?= Code::String("\"Blog.Posts\"") ?>)
                             -><?= Code::Function("Where") ?>([<?= Code::String("\"Date\"") ?> => [<?= Code::String("\"<\"") ?> => <?= Code::Variable("\$Date") ?> ?? <?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"Date\"") ?>]]])
                             -><?= Code::Function("Limit") ?>(<?= Code::Variable("\$Amount") ?> ?? <?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"Amount\"") ?>])
        ])<?= Code::Delimiter ?>
        
        
    }

}
        </code></pre>
    </section>
    <section id="Functions">
        <h3>Functions</h3>
        <p>
            The <code class="Inline">\vDesk\Pages\<?= Code::Class("Functions") ?></code>-facade provides access to custom functions located in the predefined <code class="Inline"><?= Code::Console("%InstallDir%/Server/Functions") ?></code>-directory.<br>
            Functions can be preloaded via the <code class="Inline"><?= Code::Class("Functions") ?>::<?= Code::Function("Load") ?>()</code>-method <br>
            or referencing them through the <code class="Inline"><?= Code::Class("Functions") ?>::<?= Code::Function("__callStatic") ?>()</code>-method which internally calls the Load-method and returns a closure of the function.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Keyword("&lt;?php ") ?>

<?= Code::Use ?> vDesk\Pages\<?= Code::Class("Functions") ?><?= Code::Delimiter ?>

<?= Code::Class("Functions") ?>::<?= Code::Function("Load") ?>(<?= Code::String("\"Template\"") ?>)<?= Code::Delimiter ?>

<?= Code::Keyword("?>") ?>

<?= Code::HTML("<main>") ?>
    
    <?= Code::HTML("<header>") ?>
        
        <?= Code::HTML("<nav>") ?>
        
            <?= Code::HTML("<a href=") ?><?= Code::String("\"") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Class("Functions") ?>::<?= Code::Function("URL") ?>(<?= Code::String("\"MyPage\"") ?>, <?= Code::String("\"Index\"") ?>) <?= Code::Keyword("?>") ?><?= Code::String("\"") ?><?= Code::HTML(">") ?>Home<?= Code::HTML("</a>") ?>

            <?= Code::Keyword("&lt;?php foreach") ?>(<?= Code::Variable("\$Pages") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Page") ?>): <?= Code::Keyword("?>") ?>
        
                <?= Code::HTML("<a href=") ?><?= Code::String("\"") ?><?= Code::Keyword("&lt;?=") ?> \<?= Code::Function("URL") ?>(<?= Code::String("\"MyPage\"") ?>, <?= Code::String("\"Page\"") ?>, <?= Code::Variable("\$Page") ?>-><?= Code::Field("Name") ?>) <?= Code::Keyword("?>") ?><?= Code::String("\"") ?><?= Code::HTML(">") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Page") ?>-><?= Code::Field("Title") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("</a>") ?>
        
            <?= Code::Keyword("&lt;?php endforeach; ?>") ?>
            
        <?= Code::HTML("</nav>") ?>
        
    <?= Code::HTML("</header>") ?>
    
    <?= Code::HTML("<article>") ?>
    
        <?= Code::Keyword("&lt;?=") ?> \<?= Code::Function("Template") ?>(<?= Code::String("\"MyPage/Page/Content\"") ?>, [<?= Code::String("\"Content\"") ?> => <?= Code::Variable("\$Page") ?>-><?= Code::Field("Content") ?>]) <?= Code::Keyword("?>") ?>
    
    <?= Code::HTML("</article>") ?>
    
<?= Code::HTML("</main>") ?>
        </code></pre>
    </section>
</article>