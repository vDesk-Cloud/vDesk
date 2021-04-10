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
        <h3>What is Pages?</h3>
        <p>
            "Pages" is a rather small but flexible "MVC"-framework which builds the foundation of vDesk's <a href="<?= Functions::URL("vDesk", "Index") ?>">website</a> as well as the page you're currently reading.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#ApplicationFlow">How it works</a></li>
            <li>
                <a href="#Pages">Pages (Models)</a>
                <ul class="Topics">
                    <li><a href="#Caching">Caching</a></li>
                </ul>
            </li>
            <li>
                <a href="#Templates">Templates (Views)</a>
                <ul class="Topics">
                    <li><a href="#Composition">Composition</a></li>
                    <li><a href="#PageComposition">Page based composition</a></li>
                </ul>
            </li>
            <li><a href="#Modules">Modules (Controllers)</a></li>
            <li>
                <a href="#Routing">Routing</a>
                <ul class="Topics">
                    <li><a href="#CGI">CGI Parameters</a></li>
                    <li><a href="#Routes">Configured routes</a></li>
                    <li><a href="#Rest">Rest like</a></li>
                </ul>
            </li>
            <li><a href="#Functions">Functions</a></li>
            <li><a href="#Error">Error handling</a></li>
            <li><a href="#Deploy">Deploying Pages</a></li>
        </ul>
    </header>
    <section id="ApplicationFlow">
        <h3>How it works</h3>
        <p>
            1. To trigger the framework, the server has to receive a HTTP-request of any type to the <code class="Inline"><?= Code::Console("http://host/vDesk/Server/Pages.php") ?></code>-endpoint.<br>
            The framework ships with a <code class="Inline">.htaccess</code>-file redirecting calls like <code class="Inline"><?= Code::Console("http://host/vDesk/Server/MyPage/Blog") ?></code> to the endpoint.
        </p>
        <p>
            2. The framework will try map a querystring according the rules of <a href="#Routing">routing</a> to a matching <a href="#Modules">Module</a> and method.<br>
        </p>
        <p>
           3. The <a href="#Modules">Module</a> has then to verify the submitted values and decide which <a href="#Pages">Page(s)</a>to instantiate, while Pages usually specify the <a href="#Templates">Templates</a> to display<br>
            <br>
            Pages will then compose their specified Templates enriched with
            Pages will
        </p>
        <p>
            4. A <a href="#Pages">Page</a> specifies the Templates to display, scripts and stylesheets to include and values to export to the Templates.
        </p>
        <p>
            5. A <a href="#Templates">Template</a>
        </p>
        
    </section>
    <section id="Pages">
        <h3>Pages</h3>
        <p>
            Pages are the equivalent of models and define the stylesheet- and script-files to include, the templates to compose and content to display.<br>
            Depending on its purpose, a page can represent either an entire HTLM document, a fragment of a website, or just a single HTML tag.<br>
            Pages are located in the predefined <code class="Inline"><?= Code::Console("%InstallDir%/Server/Pages") ?></code>-directory, stored in the <code class="Inline">\vDesk\Configuration\<?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"Pages\"") ?>]</code>-setting.
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
            Pages can be cached via inheriting from the <code class="Inline">\vDesk\Pages\Cached\<?= Code::Class("Page") ?></code>-class instead which serializes the output of the Page initially into a HTML-file and will then serve the precomposed Page on subsequent calls.<br>
            Cached Pages are located in the predefined <code class="Inline"><?= Code::Console("%InstallDir%/Server/Cache") ?></code>-directory, stored in the <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"Cache\"") ?>]</code>-setting.
        </p>
    </section>
    <section id="Templates">
        <h3>Templates</h3>
        <p>
            Templates are "phtml"-files written in PHP's <a target="_blank" href="https://www.php.net/manual/en/control-structures.alternative-syntax.php">alternative syntax</a> describing the structure and look of its according (partial) Page.<br>
            Templates are located in the predefined <code class="Inline"><?= Code::Console("%InstallDir%/Server/Templates") ?></code>-directory,
            stored in the <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"Templates\"") ?>]</code>-setting.
        </p>
        <p>
            If a Template is being composed as part of a Page,
            it will automatically gain access to the current Page's instance via the exported <code class="Inline"><?= Code::Variable("\$Page") ?></code> variable.
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
    <section id="PageComposition">
        <h5>Page based composition</h5>
        <p>
            Pages can be composed by passing multiple Templates to the constructor.<br>
            Templates will be appended following the order they've been specified.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("Blog") ?> <?= Code::Extends ?> \vDesk\Modules\<?= Code::Class("Module") ?> {
    
    <?= Code::BlockComment(<<<Comment
/**
     * Displays the specified blog post.
     */
Comment) ?>
        
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Post") ?>(<?= Code::Keyword("int") ?> <?= Code::Variable("\$ID") ?> = <?= Code::Null ?>): <?= Code::Class("Page") ?> {
        
        <?= Code::Variable("\$Post") ?> = <?= Code::New ?> \MyApp\Models\BlogPost(<?= Code::Variable("\$ID") ?>);
        
        <?= Code::Return ?> <?= Code::New ?> \Pages\Blog\<?= Code::Class("Post") ?>(
            [
                <?= Code::String("\"Topic\"") ?>  => <?= Code::Variable("\$Post") ?>-><?= Code::Field("Topic") ?>,
                <?= Code::String("\"Text\"") ?>   => <?= Code::Variable("\$Post") ?>-><?= Code::Field("Text") ?>,
                <?= Code::String("\"Date\"") ?>   => <?= Code::Variable("\$Post") ?>-><?= Code::Field("Date") ?>,
                <?= Code::String("\"Author\"") ?> => <?= Code::Variable("\$Post") ?>-><?= Code::Field("Author") ?>
                
            ],
            [
                <?= Code::String("\"BlogPost/Header\"") ?>,
                <?= Code::String("\"BlogPost/Content\"") ?>,
                <?= Code::String("\"BlogPost/Footer\"") ?>
                
            ]
        )<?= Code::Delimiter ?>
        
        
    }
    
}</code></pre>
    </section>
    <section id="Modules">
        <h3>Modules</h3>
        <p>
            Modules are the equivalent of Controllers and are responsible for validating requests, storing data and building Pages.<br>
            Instead of manually registering <a href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", "ModulesCommands#Commands") ?>">Commands</a>, the Pages framework provides a specialized <code class="Inline">\vDesk\Pages\<?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?></code>-Dictionary that provides access to the submitted values of the querystring.
            Modules are located in the system's <code class="Inline"><?= Code::Console("%InstallDir%/Server/Modules") ?></code>-directory, stored in the global <code class="Inline">\vDesk\<?= Code::Class("Modules") ?></code>-facade.
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

}</code></pre>
    </section>
    <section id="Routing">
        <h3>Routing</h3>
        <p>
            Routing is split into 3 subsequent stages
        </p>
    </section>
    <section id="CGI">
        <h4>CGI Parameters</h4>
        <p>In first instance, the framework checks if the querystring contains ordinary CGI-parameters</p>
        <p>
            A call in the format of <code class="Inline">?Module=Blog&Command=CreatePost&Topic=Hello world!</code> would result into the invocation of following method:
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("Blog") ?> <?= Code::Extends ?> \vDesk\Modules\<?= Code::Class("Module") ?> {

    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("CreatePost") ?>(): <?= Code::Class("Page") ?> {
        \vDesk\Pages\<?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"Topic\"") ?>]<?= Code::Delimiter ?>
        
        \vDesk\Pages\<?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"Text\"") ?>]<?= Code::Delimiter ?>
        
    }

}</code></pre>
    </section>
    <section id="Routes">
        <h4>Configured routes</h4>
        <p>
            If the request doesn't specify these values, or if they are not well formed, <br>
            the framework tries to map the querystring on a configured route-definition which are stored in the <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"Routes\"") ?>]</code>-setting.
        </p>
        <p>

            Routes can define placeholders which will be propagated in the global <code class="Inline">\vDesk\Pages\<?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?></code>-collection.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Comment("// ./Server/Settings/Routes.php") ?>

<?= Code::Return ?> [
    <?= Code::String("\"/Blog/CreatePost/{Topic}/{Text}\"") ?> => [
        <?= Code::String("\"Module\"") ?>  => <?= Code::String("\"Blog\"") ?>,
        <?= Code::String("\"Command\"") ?> => <?= Code::String("\"CreatePost\"") ?>
        
    ],
    <?= Code::String("\"/Blog/EditPost/{ID}/{Topic}/{Text}\"") ?> => [
        <?= Code::String("\"Module\"") ?>  => <?= Code::String("\"Blog\"") ?>,
        <?= Code::String("\"Command\"") ?> => <?= Code::String("\"EditPost\"") ?>
        
    ]
]<?= Code::Delimiter ?>
</code></pre>
        <p>
            A call in the format of <code class="Inline">?/Blog/EditPost/12/Topic/Hello world!/Text here...</code> would match <br>
            a route in the format of <code class="Inline">/Blog/EditPost/{ID}/{Topic}/{Text}</code> result into the invocation of following method:
        </p>
<pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("Blog") ?> <?= Code::Extends ?> <?= Code::Class("Module") ?> {

    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("EditPost") ?>(): <?= Code::Class("Page") ?> {
        <?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"ID\"") ?>]<?= Code::Delimiter ?>
        
        <?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"Topic\"") ?>]<?= Code::Delimiter ?>
        
        <?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"Text\"") ?>]<?= Code::Delimiter ?>
        
    }

}</code></pre>
    </section>
    <section id="Rest">
        <h4>Rest like</h4>
        <p>
            If no route matches the querystring, the framework tries to parse the querystring in a restful manner, checking for each segment if there may exist a matching Controller and action
            and treats every following segments as "key-value"-pairs/parameters if a Controller matches the querystring.
            If no matching Controller can be found, Pages tries to use a specified 'fallback'-route if the querystring omits any usable information.
        </p>
        <p>
            A call in the format of <code class="Inline">?/Blog/CreatePost/Topic/Hello world!/</code> would result into the invocation of following method:
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("Blog") ?> <?= Code::Extends ?> <?= Code::Class("Module") ?> {

    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("CreatePost") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Topic") ?> = <?= Code::Null ?>, <?= Code::Keyword("string") ?> <?= Code::Variable("\$Text") ?> = <?= Code::Null ?>): <?= Code::Class("Page") ?> {
        <?= Code::Class("Request") ?>::<?= Code::Variable("\$Parameters") ?>[<?= Code::String("\"Topic\"") ?>]
    }

}</code></pre>
    </section>
    <section id="Functions">
        <h3>Functions</h3>
        <p>
            The <code class="Inline">\vDesk\Pages\<?= Code::Class("Functions") ?></code>-facade provides access to custom functions located in the predefined <code class="Inline"><?= Code::Console("%InstallDir%/Server/Functions") ?></code>-directory, <br>
            accessible via the <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"Functions\"") ?>]</code>-setting.
        </p>
        <p>
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
    <section id="PredefinedFunctions">
        <h4>Predefined Functions</h4>
        <p>
            The framework ships with few predefined functions tackling common tasks.
        </p>
    </section>
    <section id="Error">
        <h4>Error handling</h4>
        <p>
            Modules can be registered as custom error-handlers to manipulate the response by sending proper status codes and displaying specific error Pages for example taking a wrong turn or informing the visitor about an internal application crash.
        </p>
        <p>
            If the system encounters a <code class="Inline">\<?= Code::Class("Throwable") ?></code>, the framework will scan the <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"ErrorHandlers\"") ?>]</code>-Dictionary for a matching Exception type<br>
            and passes the raised Exception to the configured Module's method.
        <br>
            If no Exception type matches, the framework uses the <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"ErrorHandlers\"") ?>][<?= Code::String("\"Default\"") ?>]</code>-setting as a fallback.
        </p>
        <p>
            The visibility of Error-Pages is determined by the boolean <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Pages\"") ?>][<?= Code::String("\"ShowErrors\"") ?>]</code>-setting.
        </p>
        <h5>Structure of ErrorHandlers configuration file</h5>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Comment("// ./Server/Settings/ErrorHandlers.php") ?>

<?= Code::Return ?> [
    <?= Code::String("\"Default\"") ?> => [
        <?= Code::String("\"Module\"") ?>  => <?= Code::String("\"Error\"") ?>,
        <?= Code::String("\"Command\"") ?> => <?= Code::String("\"Index\"") ?>
        
    ],
    \vDesk\IO\<?= Code::Class("FileNotFoundException") ?>::<?= Code::ClassDeclaration ?> =>  [
        <?= Code::String("\"Module\"") ?>  => <?= Code::String("\"CustomErrorHandler\"") ?>,
        <?= Code::String("\"Command\"") ?> => <?= Code::String("\"Show404\"") ?>
        
    ]
]<?= Code::Delimiter ?>
</code></pre>
        <h5>Example implementation of a custom ErrorHandler</h5>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::ClassDeclaration ?> <?= Code::Class("CustomErrorHandler") ?> <?= Code::Extends ?> <?= Code::Class("Module") ?> {

    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Show404") ?>(\vDesk\IO\<?= Code::Class("FileNotFoundException") ?> <?= Code::Variable("\$Exception") ?>): <?= Code::Class("Page") ?> { }

}</code></pre>
    </section>
    <section id="Deploy">
        <h3>Deploying Pages</h3>
        <p>
            The Pages-framework delivers a specialized <code class="Inline">\vDesk\Pages\<?= Code::Class("IPackage") ?></code>-interface for custom <a href="<?= Functions::URL("Documentation", "Page", "Packages") ?>">Packages</a>,
            defining Page-files, Templates, Modules, assets and routing configuration.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Packages<?= Code::Delimiter ?>


<?= Code::ClassDeclaration ?> <?= Code::Class("MyPage") ?> <?= Code::Extends ?> \vDesk\<?= Code::Class("Package") ?> <?= Code::Implements ?> \vDesk\Pages\<?= Code::Class("IPackage") ?>{
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"MyPage\"") ?><?= Code::Delimiter ?>
            
            
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Version") ?> = <?= Code::String("\"1.0.0\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Vendor") ?> = <?= Code::String("\"Author/company &lt;mail@example.com&gt;\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Description") ?> = <?= Code::String("\"This package provides my super cool homepage!\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Dependencies") ?> = [<?= Code::String("\"Pages\"") ?>  => <?= Code::String("\"1.1.0\"") ?>]<?= Code::Delimiter ?>
            
            
    <?= Code::Comment("//Optional license. Defaults to Ms-PL.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("License") ?> = <?= Code::String("\"MIT\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Comment("//Optional license text. Defaults to a hint on the about dialog.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("LicenseText") ?> = <?= Code::String("\"...\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Comment("//Basic file/directory structure of packages.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Files") ?> = [
        <?= Code::Self ?>::<?= Code::Const("Server") ?> => [
            <?= Code::Comment("//Controllers.") ?>
            
            <?= Code::Self ?>::<?= Code::Const("Modules") ?> => [
                <?= Code::String("\"MyPage.php\"") ?>,
                <?= Code::String("\"Blog.php\"") ?>
            
            ],
            <?= Code::Comment("//Models.") ?>
            
            <?= Code::Self ?>::<?= Code::Const("Pages") ?> => [
                <?= Code::String("\"MyPage.php\"") ?>
            
            ],
            <?= Code::Comment("//Templates.") ?>
            
            <?= Code::Self ?>::<?= Code::Const("Templates") ?> => [
                <?= Code::String("\"MyPage.php\"") ?>,
                <?= Code::String("\"MyPage/Blog\"") ?>
            
            ],
            <?= Code::Comment("//Stylesheets.") ?>
            
            <?= Code::Self ?>::<?= Code::Const("Stylesheets") ?> => [
                <?= Code::String("\"MyPage/Style.css\"") ?>
            
            ],
            <?= Code::Comment("//Images.") ?>
            
            <?= Code::Self ?>::<?= Code::Const("Images") ?> => [
                <?= Code::String("\"MyPage.Logo.png\"") ?>,
                <?= Code::String("\"MyPage/Previews\"") ?>
            
            ],
            <?= Code::Comment("//Scripts.") ?>
            
            <?= Code::Self ?>::<?= Code::Const("Scripts") ?> => [
                <?= Code::String("\"MyPage.js\"") ?>,
                <?= Code::String("\"MyPage/ExtraScripts\"") ?>
            
            ]
        ]
    ]<?= Code::Delimiter ?>
            
            
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Install") ?>(\Phar <?= Code::Variable("\$Phar") ?>, string <?= Code::Variable("\$Path") ?>): <?= Code::Void ?> {
        
        <?= Code::Comment("//Deploy resources, register routes, set host...") ?>
        
        
    }
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Uninstall") ?>(string <?= Code::Variable("\$Path") ?>): <?= Code::Void ?> {
        
        <?= Code::Comment("//Revert everything of \"Install\"-method...") ?>
        
        
    }
    
}</code></pre>
    </section>
</article>