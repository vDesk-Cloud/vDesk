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
            Pages act as the models of the framework describing the resources it needs.
            
        </p>
        <pre>
            <code><?= Code\Language::PHP ?>
<?= Code::Namespace ?> Pages\MyPage<?= Code::Delimiter ?>

                
<?= Code::ClassDeclaration ?> <?= Code::Class("Index") ?> <?= Code::Extends ?> \vDesk\Pages\<?= Code::Class("Page") ?> {
    
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?>(
        ?<?= Code::Keyword("iterable") ?> <?= Code::Variable("\$Values") ?> = [],
        ?<?= Code::Keyword("iterable") ?> <?= Code::Variable("\$Templates") ?> = [<?= Code::String("\"MyPage/Index\"") ?>],
        ?<?= Code::Keyword("iterable") ?> <?= Code::Variable("\$Stylesheets") ?> = [<?= Code::String("\"MyPage/Stylesheet\"") ?>],
        ?<?= Code::Keyword("iterable") ?> <?= Code::Variable("\$Scripts") ?> = [<?= Code::String("\"MyPage/Script\"") ?>]
    ): <?= Code::Void ?> {
        <?= Code::Parent ?>::<?= Code::Function("__construct") ?>(<?= Code::Variable("\$Values") ?>, <?= Code::Variable("\$Templates") ?>, <?= Code::Variable("\$Stylesheets") ?>, <?= Code::Variable("\$Scripts") ?>)<?= Code::Delimiter ?>
        
    }
}
            </code>
        </pre>
    </section>
    <section id="Templates">

    </section>
    <section id="Modules">
        <h3>Modules</h3>
        <p>
            Pages relies on the Modulesystem of vDesk
        </p>
    </section>
</article>