<?php use vDesk\Documentation\Code;
use vDesk\Pages\Functions; ?>
<article>
    <header>
        <h2>
            Configuration
        </h2>
        <p>
            This document describes the logic behind the global event system and how to dispatch and listen on events.<br>
            The functionality of the event system is provided by the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Events") ?>">Events</a>-package.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Introduction">Introduction</a></li>
            <li><a href="#Installation">Installation</a></li>
            <li>
                <a href="#Local">Local Configuration</a>
                <ul class="Topics">
                    <li><a href="#LocalClient">Client</a></li>
                    <li><a href="#LocalServer">Server</a></li>
                    <li><a href="#LocalManagement">Managing the local configuration</a></li>
                </ul>
            </li>
            <li>
                <a href="#Remote">Remote Configuration</a>
                <ul class="Topics">
                    <li><a href="#RemoteClient">Client</a></li>
                    <li><a href="#RemoteServer">Server</a></li>
                    <li><a href="#RemoteManagement">Managing the remote configuration</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <section id="Introduction">
        <h3>Configuration</h3>
        <p>
            Configuration
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Configuration", "Configuration.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="Installation">
        <h4>Installation</h4>
        <p>
            The configuration package doesn't require any user input while installation.
        </p>
    </section>
    <section id="Local">
        <h3>Local</h3>
        <p>
            Local
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Navigation.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="LocalClient">
        <h4>Client</h4>
        <p>
            Client
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Upload.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="LocalServer">
        <h4>Server</h4>
        <p>
            Server
        </p>
        <p>
            The configuration is located in the <code class="Inline">%installdir%/Server/Settings/DataProvider.php</code>-file and will be created filled with user input while installation.
            <br>
            The configuration values can be accessed through the global <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"DataProvider\"") ?>]</code>-settings dictionary.
        </p>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>//vDesk/Server/Settings/DataProvider.php</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(14) ?>
            <pre><code><?= Code::Return ?> [
    <?= Code::Comment("//DataProvider name [MySQL, PgSQL, MsSQL].") ?>

    <?= Code::String("\"Provider\"") ?> => <?= Code::String("\"MySQL\"") ?>,
    <?= Code::Comment("//Server name or address.") ?>

    <?= Code::String("\"Server\"") ?>  => <?= Code::String("\"localhost\"") ?>,
    <?= Code::Comment("//Server port, defaults to the standard port of the specified target server if set to null.") ?>

    <?= Code::String("\"Port\"") ?> => <?= Code::Int("3306") ?>

    <?= Code::String("\"User\"") ?>  => <?= Code::String("\"dbuser\"") ?>,
    <?= Code::String("\"Password\"") ?>  => <?= Code::String("\"dbpass\"") ?>,
    <?= Code::Comment("//DataProvider name, ignored while using the MySQL DataProvider.") ?>

    <?= Code::String("\"DataProvider\"") ?>  => <?= Code::String("\"vDesk\"") ?>,
    <?= Code::Comment("//Connection pooling true/false.") ?>

    <?= Code::String("\"Persistent\"") ?>  => <?= Code::Bool("\"false\"") ?>

]<?= Code::Delimiter ?>
</code></pre>
        </aside>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Creating local config</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(6) ?>
            <pre><code><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"CustomSettings\"") ?>] = <?= Code::New ?> Settings\Local\<?= Code::Class("Settings") ?>(
    [<?= Code::String("\"A\"") ?> => <?= Code::Int("1") ?>, <?= Code::String("\"B\"") ?> => <?= Code::Int("2") ?>, <?= Code::String("\"C\"") ?> => <?= Code::Int("3") ?>],
    <?= Code::String("\"CustomSettings\"") ?>

)<?= Code::Delimiter ?>


<?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"CustomSettings\"") ?>]-><?= Code::Function("Save") ?>()<?= Code::Delimiter ?>
</code></pre>
        </aside>
        <h5></h5>
    </section>
    <section id="Remote">
        <h3>Remote</h3>
        <p>
            The archive provides an API for registering JavaScript-classes as custom viewer controls.<br>
            To be recognized as a custom viewer, the class has to meet the following requirements:

            Files can be viewed by double clicking on them, right-clicking on them and clicking on the "Open"-item of the contextmenu and
            by selecting them and pressing the "Enter"-key or clicking on the "Open"-button of the toolbar.
        </p>
        <table>
            <tr>
                <th>Type</th>
                <th>Alias</th>
                <th>Possible validators</th>
            </tr>
            <tr>
                <td>Int</td>
                <td><code class="Inline">\vDesk\Struct\<?= Code::Class("Type") ?>::<?= Code::Const("Int") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>Float</td>
                <td><code class="Inline">\vDesk\Struct\<?= Code::Class("Type") ?>::<?= Code::Const("Float") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>String</td>
                <td><code class="Inline">\vDesk\Struct\<?= Code::Class("Type") ?>::<?= Code::Const("String") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>Boolean</td>
                <td><code class="Inline">\vDesk\Struct\<?= Code::Class("Type") ?>::<?= Code::Const("Bool") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>Enum</td>
                <td><code class="Inline">\vDesk\Struct\Extension\<?= Code::Class("Type") ?>::<?= Code::Const("Enum") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>Email</td>
                <td><code class="Inline">\vDesk\Struct\Extension\<?= Code::Class("Type") ?>::<?= Code::Const("Email") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>URL</td>
                <td><code class="Inline">\vDesk\Struct\Extension\<?= Code::Class("Type") ?>::<?= Code::Const("URL") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>Color</td>
                <td><code class="Inline">\vDesk\Struct\Extension\<?= Code::Class("Type") ?>::<?= Code::Const("Color") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>TimeSpan</td>
                <td><code class="Inline">\vDesk\Struct\Extension\<?= Code::Class("Type") ?>::<?= Code::Const("TimeSpan") ?></code></td>
                <td>String</td>
            </tr>
            <tr>
                <td>DateTime</td>
                <td><code class="Inline">\<?= Code::Class("DateTime") ?>::<?= Code::Keyword("class") ?></code></td>
                <td>String</td>
            </tr>
        </table>
    </section>
    <section id="RemoteClient">
        <h4>Client</h4>
        <p>
            Client
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Upload.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="RemoteServer">
        <h4>Server</h4>
        <p>
            Server
        </p>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Creating local config</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(6) ?>
            <pre><code><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Remote") ?>[<?= Code::String("\"CustomSettings\"") ?>] = <?= Code::New ?> Settings\Remote\<?= Code::Class("Settings") ?>(
    [
        <?= Code::String("\"A\"") ?> => <?= Code::New ?> Settings\Remote\<?= Code::Class("Setting") ?>(
            <?= Code::Int("Tag") ?>: <?= Code::String("\"A\"") ?>,
            <?= Code::Int("Value") ?>: <?= Code::String("\"A\"") ?>,
            <?= Code::Int("Type") ?>: \vDesk\Struct\<?= Code::Class("Type") ?>::<?= Code::Const("String") ?>,
            <?= Code::Int("Nullable") ?>: <?= Code::False ?>,
            <?= Code::Int("Public") ?>: <?= Code::True ?>,
            <?= Code::Int("Validator") ?>: [<?= Code::String("\"Pattern\"") ?> => <?= Code::String("\"[A-Za-z0-9]\"") ?>, <?= Code::String("\"Min\"") ?> => <?= Code::Int("10") ?>, <?= Code::String("\"Max\"") ?> => <?= Code::Int("20") ?>]
        ),
        <?= Code::String("\"B\"") ?> => <?= Code::New ?> Settings\Remote\<?= Code::Class("Setting") ?>(
            <?= Code::String("\"A\"") ?>,
            <?= Code::String("\"A\"") ?>,
            \vDesk\Struct\<?= Code::Class("Type") ?>::<?= Code::Const("Int") ?>,
            <?= Code::False ?>,
            <?= Code::True ?>,
            [<?= Code::String("\"Min\"") ?> => <?= Code::Int("0") ?>, <?= Code::String("\"Max\"") ?> => <?= Code::Int("256") ?>]]
        ),
        <?= Code::String("\"C\"") ?> => <?= Code::New ?> Settings\Remote\<?= Code::Class("Setting") ?>(
            <?= Code::String("\"C\"") ?>,
            <?= Code::String("\"C\"") ?>,
            \vDesk\Struct\Extension\<?= Code::Class("Type") ?>::<?= Code::Const("Enum") ?>,
            <?= Code::False ?>,
            <?= Code::True ?>,
            [<?= Code::String("\"A\"") ?>, <?= Code::String("\"B\"") ?>, <?= Code::String("\"C\"") ?>]
        ),
        <?= Code::String("\"C\"") ?> => <?= Code::Int("3") ?>

    ],
    <?= Code::String("\"CustomSettings\"") ?>

)<?= Code::Delimiter ?>


<?= Code::Class("Settings") ?>::<?= Code::Variable("\$Remote") ?>[<?= Code::String("\"CustomSettings\"") ?>]-><?= Code::Function("Save") ?>()<?= Code::Delimiter ?>
</code></pre>
    </section>
</article>