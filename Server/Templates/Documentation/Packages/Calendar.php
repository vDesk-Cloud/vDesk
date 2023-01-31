<?php
use vDesk\Documentation\Code;
use vDesk\Pages\Functions;
?>
<article>
    <header>
        <h2>
            Calendar
        </h2>
        <p>
            This document describes the logic behind the global event system and how to dispatch and listen on events.<br>
            The functionality of the event system is provided by the <a href="<?= \vDesk\Pages\Functions::URL("vDesk", "Page", "Packages#Events") ?>">Events</a>-package.
        </p>

        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Introduction">Introduction</a></li>
            <li><a href="#Installation">Installation</a></li>
            <li><a href="#Navigate">Navigating through the calendar</a></li>
            <li><a href="#CreateEvents">Creating Events</a></li>
            <li><a href="#EditEvents">Editing Events</a></li>
            <li><a href="#ACL">Managing access on events</a></li>
            <li><a href="#Search">Searching the calendar</a></li>
            <li><a href="#Events">Events dispatched by the calendar</a></li>
        </ul>
    </header>
    <section id="Introduction">
        <h3>Archive</h3>
        <p>
            The archive is an "explorer" like file storage
            It supports drag&drop-operations, keyboard navigation, a clipboard for copy/paste-operations
            and a contextmenu for conveniently using the archive with a mouse.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Calendar", "CalendarMonthView.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
</article>