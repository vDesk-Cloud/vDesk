<?php

use vDesk\Pages\Functions;

?>
<article class="Roadmap">
    <header>
        <h2>Roadmap</h2>
        <p>
            This is an enumeration of planned features for the future.<br>
            If you miss a feature or have any good ideas, you're welcome to contact the author or <a href="<?= Functions::URL("vDesk", "Page", "Contribute") ?>">Contribute</a> to the
            project:
        </p>
    </header>
    <section id="GeneralFeatures">
        <h3>General features</h3>
        <ul>
            <li>Workflowengine with drag&drop editor</li>
            <li>Mobile client</li>
            <li>Version control package</li>
            <li>Graphical installer</li>
            <li>PostgreSQL-, MSSQL- and Oracle-database support</li>
            <li>Process-based execution without the need of a webserver and clustered hosting of modules</li>
            <li>Template system</li>
            <li>Custom dialog boxes</li>
            <li>E-Mail client package</li>
            <li>Backup package</li>
            <li>Move DataProvider into separate package</li>
            <li>Move client into separate package</li>
            <li>Unit test package</li>
        </ul>
    </section>
    <section id="Archive">
        <h3>Archive</h3>
        <ul>
            <li>Import/export of entire directory structures</li>
            <li>Streaming of files</li>
            <li>Mounting points for different archives or filesystems like IPFS</li>
            <li>API for adding custom elements to the archive</li>
            <li>Version control for archive files</li>
            <li>Sharing files and folders via temporary links</li>
            <li>Rich text editor plugin for html and code files</li>
            <li>Contextmenu API for creation of new files</li>
            <li>Contextmenu option for creation of text files</li>
            <li>Contextmenu option for creation of PHP files</li>
            <li>Contextmenu option for creation of JavaScript files</li>
            <li>CSV-Editor with inline value editing, adding and dropping rows and columns</li>
            <li>Plugin for displaying directory contents in a separate window</li>
            <li>Synchronisation with local directories through small client with graphical drag&drop zone for a configurable directory</li>
            <li>Password protected directories</li>
            <li>File encryption</li>
            <li>Revision safe storage of files</li>
            <li>Support of playlists for the AudioPlayer plugin</li>
        </ul>
    </section>
    <section id="PinBoard">
        <h3>PinBoard</h3>
        <ul>
            <li>Coordinate system that translates the position of pinboard elements according the screen resolution</li>
            <li>API for attachable custom elements</li>
            <li>Formatting notes with basic HTML (lists, checkboxes, ...)</li>
        </ul>
    </section>
    <section id="Calendar">
        <h3>Calendar</h3>
        <ul>
            <li>CalDAV-import/-export of events</li>
            <li>Meetings with participation status tracking</li>
            <li>Configurable reminder notifications for upcoming events and meetings</li>
            <li>Customizable event text color</li>
            <li>Rich text editor for events</li>
            <li>Contextmenu option for attaching events and meetings to the pinboard</li>
        </ul>
    </section>
    <section id="Contacts">
        <h3>Contacts</h3>
        <ul>
            <li>CarDAV-import/-export of private and business contacts</li>
            <li>Contextmenu option for attaching private and business contacts to the pinboard</li>
        </ul>
    </section>
    <section id="Messenger">
        <h3>Messenger</h3>
        <ul>
            <li>AccessControlList based custom chatrooms</li>
            <li>Contextmenu option for attaching conversations to the pinboard</li>
            <li>Configurable notifications for new messages</li>
            <li>Voice-/Videochat via Web-RTC</li>
            <li>End-2-end encrypted private chats</li>
        </ul>
    </section>
    <section id="Colors">
        <h3>Colors</h3>
        <ul>
            <li>Colorizable icons</li>
        </ul>
    </section>
    <section id="Security">
        <h3>Security</h3>
        <ul>
            <li>Option to inherit permissions from existing groups</li>
            <li>Option to reset forgotten passwords</li>
        </ul>
    </section>
    <section id="Search">
        <h3>Search</h3>
        <ul>
            <li>Support for logical operators to combine multiple search values</li>
        </ul>
    </section>
    <section id="Packages">
        <h3>Packages</h3>
        <ul>
            <li>Creating setups and packages bundled with the dependencies of a package file</li>
        </ul>
    </section>
    <section id="Updates">
        <h3>Updates</h3>
        <ul>
            <li>Downloading updates via https</li>
        </ul>
    </section>
    <section id="Events">
        <h3>Events</h3>
        <ul>
            <li>Configurable storage of eventlisteners in separate directory to remove the Archive package as a hard dependency</li>
            <li>Single process EventDispatcher without database</li>
        </ul>
    </section>
    <section id="Modules">
        <h3>Modules</h3>
        <ul>
            <li>Clustered hosting of modules</li>
            <li>Process based hosting of modules</li>
        </ul>
    </section>
    <section id="Pages">
        <h3>Pages</h3>
        <ul>
            <li>Store authentication ticket in PHP's session</li>
        </ul>
    </section>
    <section id="Homepage">
        <h3>Homepage</h3>
        <ul>
            <li>Implement changelog</li>
        </ul>
    </section>
</article>