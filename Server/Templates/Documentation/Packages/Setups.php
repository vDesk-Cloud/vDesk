<?php

use vDesk\Documentation\Code;
use vDesk\Pages\Functions;

?>
<article class="Packages">
    <header>
        <h2>Packages, updates and setups</h2>
        <p>
            This document describes the specifications of packages, updates and setups and how to create custom versions.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li>
                <a href="#Setups">Setups</a>
                <ul class="Topics">
                    <li>
                        <a href="#SetupCreation">Creating setups</a>
                        <ul class="Topics">
                            <li><a href="#Exclude">Excluding packages</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </header>
    <section id="Setups">
        <h3 id="Setups">Setups</h3>
        <p>
            Setups are executable PHAR-archives bundled with the resources defined by all bundled package <a href="#PackageManifest">manifest</a> classes
            named "Setup.phar"
            with a stub that calls the <code class="Inline">\Modules\<?= Code::Class("Setup") ?>::<?= Code::Function("Install") ?>()</code> method upon execution.
        </p>
    <pre><code>--- <?= Code::BlockComment("Setup.phar") ?>

    |
      --- <?= Code::BlockComment("Client") ?> <?= Code::Comment("//Client-side resources.") ?>

          |
            --- <?= Code::BlockComment("...") ?>

    |
      --- <?= Code::BlockComment("Server") ?> <?= Code::Comment("//Server-side resources.") ?>

          |
            --- <?= Code::BlockComment("Lib") ?> <?= Code::Comment("//Server-side library files.") ?>

                |
                  --- <?= Code::BlockComment("vDesk") ?> <?= Code::Comment("//vDesk library.") ?>

                      |
                        --- <?= Code::BlockComment("Packages") ?> <?= Code::Comment("//Package manifest class files.") ?>

          |
            --- <?= Code::BlockComment("Modules") ?> <?= Code::Comment("//Server-side modules.") ?>
    </code></pre>
    </section>
    <section id="SetupCreation">
        <h4>Creating setups</h4>
        <p>
            The creation of setups requires the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Setup") ?>">Setup</a> and
            <a href="<?= Functions::URL("vDesk", "Page", "Packages#Console") ?>">Console</a>-packages to be installed
            (these are bundled by default in the official releases).<br>
            To create a custom setup press <code class="Inline">° / Shift + ^</code> to open the client side console
            and enter one of the following commands:
        </p>
        <pre><code><span class="Console">Call -M=Setup -C=Create [-Path=E:\Development\Setups]
Call -Module=Setup -Command=Create [-Path=/var/www/htdocs/vDesk/Server]</span></code></pre>
        <p>
            This will create a "Setup.phar"-file bundled with all currently installed packages at the optionally specified path on the server.<br>
            If the "Path"-parameter is omitted, the setup file will be created in the system's "Server"-directory.
        </p>
    </section>
    <section id="Exclude">
        <h5>Excluding packages</h5>
        <p>
            To exclude certain packages, a comma separated list of packages can be specified that won't get bundled in the setup.
        </p>
        <pre><code><span class="Console">Call -M=Setup -C=Create [-Path=%TargetDir%, -Exclude=Pages, Homepage, Documentation, ...]
Call -Module=Setup -Command=Create [-Path=%TargetDir%, -Exclude=%A%, %B%, ...]</span></code></pre>
        <p>
            Alternatively, a JSON array of packages can be specified.
        </p>
        <pre><code><span class="Console">Call -M=Setup -C=Create -Exclude=["Pages", "Homepage", "Documentation", "Contacts", "Messenger"]</span></code></pre>

    </section>
</article>