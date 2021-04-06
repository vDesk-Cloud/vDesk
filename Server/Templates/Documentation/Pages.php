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
        </ul>
    </header>
    <section id="Routing">
        <h3>Routing</h3>
        <p>
            Application flow:
            Pages first checks, if the request contains ordinary CGI-parameters like "?Module=ExampleModule&Command=ExampleCommand&param1=...
            If the request doesn't specify these values, or if they are not well formed, Pages tries to map the querystring on a configured route-definition.
            If no route matches the querystring, Pages tries to parse the querystring in a restful manner, checking for each segment if there may exist a matching Controller and action
            and treats every following segments as "key-value"-pairs/parameters if a Controller matches the querystring.
            If no matching Controller can be found, Pages tries to use a specified 'fallback'-route if the querystring omits any usable information.
        </p>
    </section>
    <section id="Pages">
        <h3>Pages</h3>
        <p>
            Pages are the equivalent of models and define the stylesheet- and script-files to include, the templates to compose and content to display.
            
        </p>
        <p>
            Depending on its purpose, a page can represent either an entire HTLM document, a fragment of a website, or just a single HTML tag.
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
    <section id="Templates">
        <pre><code><?= Code\Language::PHP ?>
<?= Code::HTML("<!DOCTYPE html>") ?>

<?= Code::HTML("<html>") ?>

<?= Code::HTML("<head>") ?>

    <?= Code::HTML("<title>") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Title") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("</title>") ?>
    
    <?= Code::Keyword("&lt;?php foreach") ?>(<?= Code::Variable("\$Page") ?>-><?= Code::Field("Stylesheets") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Stylesheet") ?>): <?= Code::Keyword("?>") ?>
        
        <?= Code::HTML("<link rel=") ?><?= Code::String("\"stylesheet\"") ?> <?= Code::HTML("href=") ?><?= Code::String("\"") ?><?= Code::Keyword("&lt;?=") ?> Functions::<?= Code::Function("Stylesheet") ?>(<?= Code::Variable("\$Stylesheet") ?>) <?= Code::Keyword("?>") ?><?= Code::String("\"") ?><?= Code::HTML("/>") ?>
        
    <?= Code::Keyword("&lt;?php endforeach; ?>") ?>
    
    <?= Code::Keyword("&lt;?php foreach") ?>(<?= Code::Variable("\$Page") ?>-><?= Code::Field("Scripts") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Script") ?>): <?= Code::Keyword("?>") ?>
        
        <?= Code::HTML("<script src=") ?><?= Code::String("\"") ?><?= Code::Keyword("&lt;?=") ?> Functions::<?= Code::Function("Script") ?>(<?= Code::Variable("\$Script") ?>) <?= Code::Keyword("?>") ?><?= Code::String("\"") ?><?= Code::HTML("/>") ?>
        
    <?= Code::Keyword("&lt;?php endforeach; ?>") ?>
    
<?= Code::HTML("</head>") ?>

<?= Code::HTML("<body>") ?>
    
    <?= Code::HTML("<main>") ?>
    
        <?= Code::HTML("<header>") ?>
        
            <?= Code::HTML("<h1>") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Title") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("</h1>") ?>
            
            <?= Code::HTML("<nav>") ?>
        
                <?= Code::Keyword("&lt;?php foreach") ?>(<?= Code::Variable("\$Pages") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Page") ?>): <?= Code::Keyword("?>") ?>
        
                    <?= Code::HTML("<a") ?> <?= Code::HTML("href=") ?><?= Code::String("\"") ?><?= Code::Keyword("&lt;?=") ?> Functions::<?= Code::Function("URL") ?>(<?= Code::String("\"MyPage\"") ?>, <?= Code::String("\"Page\"") ?>, <?= Code::Variable("\$Page") ?>) <?= Code::Keyword("?>") ?><?= Code::String("\"") ?><?= Code::HTML(">") ?><?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Page") ?> <?= Code::Keyword("?>") ?><?= Code::HTML("</a>") ?>
        
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
    <section id="Reusing">
        <h4>Reusing templates</h4>
        <p>
            Pages relies on the Modulesystem of vDesk
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
        
            Published at <?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Date") ?> <?= Code::Keyword("?>") ?>, by <?= Code::Keyword("&lt;?=") ?> <?= Code::Variable("\$Author") ?> <?= Code::Keyword("?>") ?>.
        <?= Code::HTML("</p>") ?>
        
    <?= Code::HTML("</footer>") ?>

<?= Code::HTML("</article>") ?>
        </code></pre>
    </section>
    <section id="Modules">
        <h3>Modules</h3>
        <p>
            Pages relies on the Modulesystem of vDesk
        </p>
    </section>
</article>