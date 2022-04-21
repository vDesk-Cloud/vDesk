<?php

use vDesk\Documentation\Code;
use vDesk\Pages\Functions;

?>
<article class="Security">
    <header>
        <h2>Security</h2>
        <p>
            This document describes the specifications of packages, updates and setups.
            <br>
            For further information about creating packages and setups, visit the <a href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", "CustomReleases") ?>">Custom
                releases</a>-tutorial.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Concepts">Concepts</a></li>

            <li>
                <a href="#Users">Users</a>
                <ul class="Topics">
                    <li><a href="#UserManagement">Managing users</a></li>
                    <li><a href="#UpdateManifest">Manifest</a></li>
                </ul>
            </li>
            <li>
                <a href="#Groups">Groups</a>
                <ul class="Topics">
                    <li><a href="#GroupManagement">Managing groups</a></li>
                    <li><a href="#GroupRights">Adding or deleting rights</a></li>
                </ul>
            </li>
            <li>
                <a href="#ACL">Access Control Lists</a>
                <ul class="Topics">
                    <li><a href="#Precedence">Rights predecence</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <section id="Concepts">
        <h3>Concepts</h3>
        <p>
            Security is managed through groups for global rights and "Access Control Lists" for single elements.
        </p>
    </section>
    <section id="Users">
        <h3>Users</h3>
    </section>
    <section id="Groups">
        <h3>Groups</h3>
    </section>
    <section id="ACL">
        <h3>Access Control Lists</h3>
    </section>
</article>