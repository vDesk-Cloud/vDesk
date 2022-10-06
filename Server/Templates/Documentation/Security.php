<?php

use vDesk\Documentation\Code;
use vDesk\Pages\Functions;

?>
<article class="Security">
    <header>
        <h2>Security</h2>
        <p>
            This document describes the security concepts of vDesk, how to administrate users and groups as well as managing access to entities.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Concepts">Concepts</a></li>

            <li>
                <a href="#Users">Users</a>
                <ul class="Topics">
                    <li><a href="#UserAdministration">Administrating users</a></li>
                </ul>
            </li>
            <li>
                <a href="#Groups">Groups</a>
                <ul class="Topics">
                    <li><a href="#GroupPermissions">Creating/deleting group permissions</a></li>
                </ul>
            </li>
            <li>
                <a href="#ACL">Access Control Lists</a>
                <ul class="Topics">
                    <li><a href="#Entities">Access control entities</a></li>
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
        <p>
            Authentication is performed via sending a "Login"-Command with a username or email-address and password to the "Security" module whose credentials are validated through the
            <code class="Inline">\Modules\<?= Code::Class("Security") ?>::<?= Code::Function("Login") ?>()</code>-method.<br>
            This will return a unique session ticket with a limited lifespan that will be automatically validated through the <code class="Inline"><?= Code::Class("Security") ?>::<?= Code::Function("ReLogin") ?>()</code>-method
            if it's being passed through the CGI-parameters of any command that requires authentication.
        </p>
        <p>
            The session ticket's lifespan is automatically reset to the maximum time on each execution of a command that requires authentication while it's valid;<br>
            otherwise the system throws a <code class="Inline">\vDesk\Security\<?= Code::Class("TicketExpiredException") ?></code> which will trigger the dispatch of a "ticketexpired"-event on the client side.<br>
            The lifetime of a session ticket can be configured through the <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Remote") ?>[<?= Code::String("\"Security\"") ?>][<?= Code::String("\"SessionLifeTime\"") ?>]</code>-setting.<br>
            Existing session tickets will be rendered invalid by a second login of the associated account.
        </p>
        <p>
            After login, the user instance is propagated through the global
            <code class="Inline">\vDesk\Security\<?= Code::Class("User") ?>::<?= Code::Variable("\$Current") ?></code> and
            <code class="Inline">vDesk.Security.<?= Code::Class("User") ?>.<?= Code::Field("Current") ?></code>-properties.<br>
            The server will only propagate the global property once after login, so subsequent calls to <code class="Inline"><?= Code::Class("Security") ?>::<?= Code::Function("Login") ?>()</code> will preserve the current session.
        </p>
        <p>
            The Security-Module keeps track of the amount of failed login attempts and will automatically lock the account if a certain amount has been reached preventing further attempts.<br>
            The amount of failed logins is configured through the <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Remote") ?>[<?= Code::String("\"Security\"") ?>][<?= Code::String("\"FailedLoginCount\"") ?>]</code>-setting
            and can be reset to 0 in the administration window's user editor.
        </p>
    </section>
    <section id="UserAdministration">
        <h4>Administrating users</h4>
        <p>
            Users can be administrated under the "Users"-tab of the administration window.<br>
            Creating, updating and deleting users requires the current user to be a member of a group with the <code class="Inline">CreateUser</code>-, <code class="Inline">UpdateUser</code>- and <code class="Inline">DeleteUser</code>-permissions granted.
        </p>
        <h5>Example of editing users</h5>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation/Users.png") ?>" alt="Image showing the administrative view of the group- and rights management">
        </aside>
        <p>
            Users can change their email-address, password and locale in the client side settings menu under the "User settings"-tab.
        </p>
    </section>
    <section id="Groups">
        <h3>Groups</h3>
        <p>
            Groups are dictionaries of global permissions which target more common tasks like being able to access the administration window, using the calendar or editing contacts for example.<br>
        </p>
        <p>
            Groups can be administrated under the "Groups"-tab of the administration window.<br>
            Creating, updating and deleting groups requires the current user to be a member of a group with the <code class="Inline">CreateGroup</code>-, <code class="Inline">UpdateGroup</code>- and <code class="Inline">DeleteGroup</code>-permissions granted.
        </p>
        <h5>Example of editing groups</h5>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation/Groups.png") ?>"  alt="Image showing the administrative view of the group- and rights management">
        </aside>
    </section>
    <section id="GroupPermissions">
        <h4>Creating/deleting group permissions</h4>
        <p>
            The permissions of groups are defined over the database where each column of the <code class="Inline"><?= Code::Const("Security") ?>.<?= Code::Field("Groups") ?></code>-table
            represents a certain permission.<br>
            They can only be created or deleted on the server by calling the Security module's <code class="Inline"><?= Code::Class("Security") ?>::<?= Code::Function("CreatePermission") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Name") ?>, <?= Code::Keyword("bool") ?> <?= Code::Variable("\$Default") ?>)</code>- and
            <code class="Inline"><?= Code::Class("Security") ?>::<?= Code::Function("DeletePermission") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Name") ?>)</code>-methods.<br>
        </p>
        <p>
            Both operations require the current user to be a member of a group with the <code class="Inline">UpdateGroup</code>-permission granted.
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
            <br>These permissions are separated between direct user and group based permissions.
        </p>
        <p>
            Permissions of ACLs follow the principle of "permission over prohibition", this means a single granted permission overrides any prohibition, no matter how many of them exist.
        </p>
        <p>
            Deleting a user or a group results in the deletion of all associated ACL entries.
        </p>
        <h5>Group editor in the administration window</h5>
        <h5>Example of editing ACLs</h5>
        <section style="text-align: center">
            <aside class="Image" onclick="this.classList.toggle('Fullscreen')">
                <img src="<?= Functions::Image("Documentation/ACL.png") ?>" alt="Image showing the Access Control List editor">
            </aside>
        </section>
        <p>
            Compared to windows ACLs, the "list" and "execute" permissions are usually (package dependent) merged into the "read" permission.
        </p>
    </section>
    <section id="Entities">
        <h3>Access control entities</h3>
        <p>
            To secure entities, the Security-package delivers an abstract <code class="Inline">\vDesk\Security\<?= Code::Class("AccessControlledModel") ?></code>-class
            that implements the <code class="Inline">\vDesk\Data\<?= Code::Class("IModel") ?></code>-interface
            and defines a public <code class="Inline"><?= Code::Class("AccessControlledModel") ?>-><?= Code::Field("AccessControlList") ?></code>-property,
            an abstract <code class="Inline"><?= Code::Class("AccessControlledModel") ?>::<?= Code::Function("GetACLID") ?>()</code>-method
            and extends the <code class="Inline"><?= Code::Class("IModel") ?>::<?= Code::Function("Fill") ?>()</code>-, <code class="Inline"><?= Code::Class("IModel") ?>::<?= Code::Function("Save") ?>()</code>-
            and <code class="Inline"><?= Code::Class("IModel") ?>::<?= Code::Function("Delete") ?>()</code>-methods
            accepting an optional <code class="Inline">\vDesk\Security\<?= Code::Class("User") ?></code>-parameter to check for permissions on the desired actions.
        </p>
        <p>
            Upon accessing the <code class="Inline"><?= Code::Class("AccessControl") ?>-><?= Code::Field("AccessControlList") ?></code>-property, the trait will automatically load
            and fill an instance of the <code class="Inline">\vDesk\Security\<?= Code::Class("AccessControlList") ?></code>-class identified by the value returned of the <code class="Inline"><?= Code::Function("GetACLID") ?>()</code>-method.
            The property will be initialized with a default ACL if there's currently no ACL ID existing.
        </p>
        <p>
            Permissions of ACLs follow the principle of "permission over prohibition", this means a single granted permission overrides any prohibition, no matter how many of them exist.
        </p>
        <p>
           ACLs provide a set of shorthand properties to read-, write- and delete-permission according the current logged in user.
        </p>
        <p>
            Compared to windows ACLs, the "list" and "execute" permissions are usually (package dependent) merged into the "read" permission.
        </p>
    </section>
</article>