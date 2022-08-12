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
        <p>
            In general, the permissions follow the principle of "permission over prohibition", this means if a user has
        </p>
    </section>
    <section id="Users">
        <h3>Users</h3>
    </section>
    <section id="Groups">
        <h3>Groups</h3>
        <p>
            Groups are dictionaries of global permissions
        </p>
        <aside onclick="this.classList.toggle('Fullscreen')">
            <img src="<?= Functions::Image("Documentation/Groups.png") ?>" alt="Image showing the administrative view of the group- and rights management">
        </aside>
    </section>
    <section id="GroupPermissions">
        <h4>Adding/removing Group permissions</h4>
        <p>
            The permissions of a group are defined over the database where each column of the <code class="Inline"><?= Code::Const("Security") ?>.<?= Code::Field("Groups") ?></code>-table
            represents a certain permission.
        </p>
        <p>
            Permissions can be created or deleted by calling the Security Module's <code class="Inline"><?= Code::Class("Security") ?>::<?= Code::Function("CreatePermission") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Name") ?>, <?= Code::Keyword("bool") ?> <?= Code::Variable("\$Default") ?>)</code>- and
            <code class="Inline"><?= Code::Class("Security") ?>::<?= Code::Function("DeletePermission") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Name") ?>)</code>-methods.
        </p>
        <p>
            Both operations require the current user to be a member of a group which has the "UpdateGroup"-permission been granted.
        </p>
        <aside class="Note">
            <h4>Note</h4>
            <p>
                The default value of each permission is used as an initial reference while creating the "Everyone"-group on setup;
                after that the group's permissions will be used as a template for creating new groups.<br>
                So keep in mind setting the default permissions in the perspective of a normal user.
            </p>
        </aside>
    </section>
    <section id="ACL">
        <h3>Access Control Lists</h3>
        <p>
            "Access Control Lists" or "ACLs" are collections of individual permissions on certain "access controlled" entities.
        </p>
    </section>
</article>