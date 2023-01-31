<?php use vDesk\Documentation\Code;
use vDesk\Pages\Functions; ?>
<article>
    <header>
        <h2>
            PinBoard
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
                <a href="#Notes">Notes</a>
                <ul class="Topics">
                    <li><a href="#CreateNotes">Creating notes</a></li>
                    <li><a href="#UpdateNotes">Updating notes</a></li>
                    <li><a href="#DeleteNotes">Deleting notes</a></li>
                </ul>
            </li>
            <li>
                <a href="#Attachments">Attachments</a>
                <ul class="Topics">
                    <li><a href="#CreateAttachments">Creating attachments</a></li>
                    <li><a href="#UpdateAttachments">Updating attachments</a></li>
                    <li><a href="#DeleteAttachments">Deleting attachments</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <section id="Introduction">
        <h3>Introduction</h3>
        <p>
            The PinBoard is kewl.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "PinBoard", "PinBoard.png") ?>" alt="Image showing the PinBoard">
        </aside>
        <aside class="Note">
            <h4>Note</h4>
            <p>
                The PinBoard currently uses the raw offset-values of elements for placement,
                this means elements placed on bigger screens may occur out-of-bounds on smaller resolutions.<br>
                This issue will be solved in the future via translating the offsets into a coordinate system.
            </p>
        </aside>
        <p>
            Any action that updates the state of a pinboard element will trigger a 3-second-countdown
            before the state of the element gets persisted to the database to avoid unnecessary traffic;
            the countdown will reset each time the element is being updated while running.<br>
            This includes moving notes and attachments as well as changing the color, text and size of a note.
        </p>
    </section>
    <section id="Installation">
        <h4>Installation</h4>
        <p>
            The PinBoard package doesn't require any user input and can be installed from setup or on demand.
        </p>
    </section>
    <section id="Notes">
        <h4>Notes</h4>
        <p>
            Notes are free placeable and scalable text fields which can be individually colorized.

        </p>
    </section>
    <section id="CreateNotes">
        <h5>Creating notes</h5>
        <p>
            New notes can be created by clicking on the "New note"-button of the toolbar
            or by right-clicking on the pinboard and choosing the "Note"-option of the "New"-submenu of the contextmenu.<br>
            New notes will be created by default in a yellow coloration in the top left corner of the pinboard.
        </p>
        <p>
            To prevent note spam from "funny" coworkers, the options described above will only focus the last created note until its text has been changed.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "PinBoard", "CreateNote.png") ?>" alt="Image showing how to create a new note on the PinBoard.">
        </aside>
    </section>
    <section id="UpdateNotes">
        <h5>Updating notes</h5>
        <p>
            Notes inherit from the <a href="<?= Functions::URL("Documentation", "Topic", "Controls#DynamicBox") ?>">DynamicBox</a>-Control
            and therefore can be moved around by dragging the note on its header and resized by dragging on the corners and borders of the note.<br>

            The Contacts package doesn't require any user input and can be installed from setup or on demand.
        </p>
    </section>
    <section id="DeleteNotes">
        <h5>Deleting notes</h5>
        <p>
            Notes can be deleted by selecting them and clicking on the "Delete"-button of the toolbar
            or by right-clicking on their header and choosing the "Delete"-option of the contextmenu.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "PinBoard", "DeleteNote.png") ?>" alt="Image showing the options to delete pinboard notes">
        </aside>
    </section>
    <section id="Attachments">
        <h4>Attachments</h4>
        <p>
            Attachments are a plugin for the archive package, allowing to "attach" archive elements and directly view in the case of a file
            or conveniently jump to a folder from the pinboard.<br>
            The pinboard won't check for changed permissions, so attachments may lead to a dead end.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "PinBoard", "Attachments.png") ?>" alt="Image showing an overview over pinboard attachments">
        </aside>
    </section>
    <section id="CreateAttachments">
        <h5>Creating attachments</h5>
        <p>
            The pinboard hooks into the archive's contextmenu and adds an "Add to pinboard"-item,
            this is available via right-clicking on the archive element to attach.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "PinBoard", "CreateAttachment.png") ?>" alt="Image showing how to attach files and folders to the pinboard">
        </aside>
    </section>
    <section id="UpdateAttachments">
        <h5>Updating attachments</h5>
        <p>
            Attachments inherit from the <a href="<?= Functions::URL("Documentation", "Topic", "Controls#FloatingBox") ?>">FloatingBox</a>-Control
            and therefore can only be moved around by dragging the attachment.
        </p>
    </section>
    <section id="DeleteAttachments">
        <h5>Deleting attachments</h5>
        <p>
            Attachments can be deleted by selecting them and clicking on the "Delete"-button of the toolbar
            or by right-clicking on them and choosing the "Delete"-option of the contextmenu.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "PinBoard", "DeleteAttachment.png") ?>" alt="Image showing the options to delete pinboard attachments">
        </aside>
    </section>
</article>