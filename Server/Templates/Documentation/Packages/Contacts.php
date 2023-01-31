<?php use vDesk\Documentation\Code;
use vDesk\Pages\Functions; ?>
<article>
    <header>
        <h2>
            Contacts
        </h2>
        <p>
            This document describes the logic behind the global event system and how to dispatch and listen on events.<br>
            The functionality of the event system is provided by the <a href="<?= \vDesk\Pages\Functions::URL("vDesk", "Page", "Packages#Events") ?>">Events</a>-package.
        </p>

        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Introduction">Introduction</a></li>
            <li><a href="#Installation">Installation</a></li>
            <li>
                <a href="#Contacts">Contacts</a>
                <ul class="Topics">
                    <li><a href="#CreateContacts">Creating contacts</a></li>
                    <li><a href="#UpdateContacts">Updating contacts</a></li>
                    <li><a href="#DeleteContacts">Deleting contacts</a></li>
                </ul>
            </li>
            <li>
                <a href="#BusinessContacts">Business contacts</a>
                <ul class="Topics">
                    <li><a href="#CreateBusinessContacts">Creating business contacts</a></li>
                    <li><a href="#UpdateBusinessContacts">Updating business contacts</a></li>
                    <li><a href="#DeleteBusinessContacts">Deleting business contacts</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <section id="Introduction">
        <h3>Introduction</h3>
        <p>
            The Contacts package contains an ACL based contacts management system for personal and business contacts.
        </p>
        <h5>Example of dispatching an event</h5>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Contacts", "Contacts.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="Installation">
        <h4>Installation</h4>
        <p>
            The Contacts package doesn't require any user input and can be installed from setup or on demand.
        </p>
    </section>
    <section id="Contacts">
        <h4>Contacts</h4>
        <p>
            Contacts are variable datasets of personal data like the full name or address of a person.<br>
            Contacts rely on "Access Control Lists" of the <a href="./Security">Security</a>-package for access management,
            allowing to create totally private and public shared contacts in the same system.
        </p>
        <h5>Example of dispatching an event</h5>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Contacts", "Contacts.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
</article>