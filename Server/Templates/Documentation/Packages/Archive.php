<?php use vDesk\Documentation\Code;
use vDesk\Pages\Functions; ?>
<article>
    <header>
        <h2>
            Archive
        </h2>
        <p>
            This document describes the logic behind the global event system and how to dispatch and listen on events.<br>
            The functionality of the event system is provided by the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Events") ?>">Events</a>-package.
        </p>

        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Introduction">Introduction</a></li>
            <li><a href="#Installation">Installation</a></li>
            <li><a href="#Configuration">Configuration</a></li>
            <li><a href="#Navigation">Navigating</a></li>
            <li><a href="#Upload">Uploading files to the archive</a></li>
            <li><a href="#Download">Downloading files</a></li>
            <li>
                <a href="#View">Viewing files</a>
                <ul class="Topics">
                    <li><a href="#CustomViewer">Custom file viewers</a></li>
                </ul>
            </li>
            <li><a href="#Copy">Copying files and folders</a></li>
            <li><a href="#Move">Moving files and folders</a></li>
            <li>
                <a href="#Edit">Editing files</a>
                <ul class="Topics">
                    <li><a href="#CustomEditor">Custom file editors</a></li>
                </ul>
            </li>
            <li><a href="#Rename">Renaming files and folders</a></li>
            <li><a href="#ACL">Managing access of files and folders</a></li>
            <li><a href="#SystemFolder">System folder</a></li>
            <li><a href="#Search">Search</a></li>
            <li><a href="#Events">Events dispatched by the archive</a></li>
        </ul>
    </header>
    <section id="Introduction">
        <h3>Archive</h3>
        <p>
            The archive is an "explorer" like file storage
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Archive.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="Installation">
        <h4>Installation</h4>
        <p>
            The archive package con only be installed as part of a setup.
        </p>
    </section>
    <section id="Configuration">
        <h4>Configuration</h4>
        <p>
            The archive package registers the following <a href="<?= Functions::URL("Documentation","Package", "Configuration") ?>">configuration</a> settings:
        </p>
        <table>
            <tr>
                <th>Location</th>
                <th>Domain</th>
                <th>Tag</th>
                <th>Type</th>
                <th>Public</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>Local</td>
                <td>Archive</td>
                <td>Directory</td>
                <td>String</td>
                <td>-</td>
                <td>The folder where uploaded files will be saved in</td>
            </tr>
            <tr>
                <td>Remote</td>
                <td>Archive</td>
                <td>UploadMode</td>
                <td>Enum</td>
                <td>x</td>
                <td>Determines whether multiple files will be upload parallel or in queue. (Currently unused)</td>
            </tr>
        </table>
    </section>

    <section id="Navigation">
        <h4>Navigation</h4>
        <p>
            The execution of several commands causes certain global events to be triggered;
            for example: the deletion of an Entry from the Archive will trigger a global <code class="Inline">vDesk.Archive.Element.Deleted</code>-event.<br>
        </p>
        <p>
            Multiple elements can be selected via holding down the left mouse button and drawing a selection rectangle over the target elements
            or while holding down the left "CTRL"-key and clicking on single elements.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Select.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="Upload">
        <h4>Uploading files to the archive</h4>
        <p>
            Files can be uploaded via dragging and dropping them on a target folder-element, this will create a temporary element for each dropped file
            containing a yellow progressbar that tracks the current upload status.
            The progressbar will turn green after the file has been successfully uploaded, otherwise it will turn red if any error occurred
            and disappear after a few seconds.<br>
            Alternatively a click on the "Add file"-button of the toolbar will open a file-dialog to upload a file to the current opened folder.<br>
            Uploading files requires the current user to have "Write"-permissions on the target folder-element.
        </p>
    </section>
    <section id="Download">
        <h4>Downloading files</h4>
        <p>
            Files can be downloaded via right-clicking them and choosing the "Save" option in the context menu.<br>
            This will require the user to hae
        </p>
            <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
                <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Save.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
            </aside>
        <aside class="Note">
            <h4>Note</h4>
            <p>
                Due to technical reasons, the file is first being downloaded into the allocated RAM of the browser and then served over a dialog for finally saving it.<br>
                This may limit the maximum size of downloads to the browser's settings or available RAM on systems without swap files for example.
            </p>
        </aside>
        <p>

        </p>
    </section>
    <section id="View">
        <h3>Viewing files</h3>
        <p>
            The event system supports multiple ways of registering event listeners.
            These are directly attached listeners via passing an event name and a closure to the <code class="Inline"><?= Code::Class("Events") ?>::<?= Code::Function("AddEventListener") ?>()</code>-method
            and file-based event listeners, which will be loaded and executed upon schedule.
        </p>
    </section>
    <section id="Copy">
        <h3>Copy files</h3>
        <p>
            The event system supports multiple ways of registering event listeners.
            These are directly attached listeners via passing an event name and a closure to the <code class="Inline"><?= Code::Class("Events") ?>::<?= Code::Function("AddEventListener") ?>()</code>-method
            and file-based event listeners, which will be loaded and executed upon schedule.
        </p>
        <h5>
            Example of registering an event listener
        </h5>
        <pre><code><?= Code\Language::PHP ?>
\vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Events") ?>()::<?= Code::Function("AddEventListener") ?>(
    <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>,
    <?= Code::Keyword("fn") ?>(\vDesk\Events\<?= Code::Class("Event") ?> <?= Code::Variable("\$Event") ?>) => <?= Code::Class("Log") ?>::<?= Code::Function("Info") ?>(<?= Code::String("\"Element '") ?>{<?= Code::Variable("\$Event") ?>-><?= Code::Field("Element") ?>-><?= Code::Field("Name") ?>}<?= Code::String("' has been deleted\"") ?>)
)<?= Code::Delimiter ?>
</code></pre>
        <p>
            File-based event listeners are simple PHP files which must return a tuple array of the event's name to listen on and a callback closure
            which are stored in an "Events"-folder that is by default located in the "Server"-directory or optionally in the Archive package's "System"-directory.<br>
            The Events package will scan on installation if the setup is bundled with the Archive package and thus asks if the event listener files shall be stored and searched on the filesystem, in the archive or both.<br>
            The storage mode is stored in the local  <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Events\"") ?>][<?= Code::String("\"Mode\"") ?>]</code>-setting.
        </p>
        <p>
            To match an event, the listener file must contain the name of the desired event in its file name on the beginning.
            For example: if an event is named "Security.User.Created", the filename of the event listener may be called "Security.User.Created.GreetNewUser.php".
        </p>
        <h5>
            Example of an event listener file
        </h5>
        <pre><code><?= Code\Language::PHP ?>
                <?= Code::PHP ?>


                <?= Code::Use ?> \vDesk\Archive\Element\<?= Code::Class("Deleted") ?><?= Code::Delimiter ?>


                <?= Code::Return ?> [
    <?= Code::Class("Deleted") ?>::<?= Code::Const("Name") ?>,
    <?= Code::Keyword("fn") ?>(<?= Code::Class("Deleted") ?> <?= Code::Variable("\$Event") ?>) => <?= Code::Class("Log") ?>::<?= Code::Function("Info") ?>(<?= Code::String("\"Element '") ?>{<?= Code::Variable("\$Event") ?>-><?= Code::Field("Element") ?>-><?= Code::Field("Name") ?>}<?= Code::String("' has been deleted\"") ?>)
]<?= Code::Delimiter ?>
</code></pre>
    </section>
    <section id="Move">
        <h3>Move files and folders</h3>
        <p>
            Files and folders can be moved by selecting and dragging them onto a target folder
            or by clicking on the "Cut"-button while selected and clicking on the "Paste"-button in the target folder.<br>
            The same action can be achieved by selecting elements and pressing "CTRL+X" and "CTRL+V" in the target folder.<br>
            This operation will require "write"-access on the target elements and destination folder.
        </p>
    </section>
    <section id="Edit">
        <h3>Edit files</h3>
        <p>
            The event system supports multiple ways of registering event listeners.
            These are directly attached listeners via passing an event name and a closure to the <code class="Inline"><?= Code::Class("Events") ?>::<?= Code::Function("AddEventListener") ?>()</code>-method
            and file-based event listeners, which will be loaded and executed upon schedule.
        </p>
    </section>
    <section id="Rename">
        <h3>Rename files</h3>
        <p>
            Files and folders can be renamed via right-clicking on them to open the contextmenu and clicking on the "Rename"-item.<br>
            This will make the name-label of the desired element editable and saves any changes when a click occurred outside the element
            or the enter-button has been pressed.<br>
            Renaming elements requires the current user to have "Write"-permissions on the desired element.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Rename.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="Attributes">
        <h3>Attributes</h3>
        <p>
            To manage permissions on files and folders, the archive package integrates an ACL-editor in the "Permissions"-tab of the "Attributes"-window.
            To open the "Attributes"-window, simply right-click on an archive element and click the "Attributes"-item of the contextmenu
            or select an element and click on the "Attributes"-button in the toolbar.
        </p>
        <p>
            The "Permissions"-tab will only be displayed if the current user is a member of a group with the "ReadAccessControlList" granted.
        </p>
        <p>
            The archive uses AccessControlLists from the "Security"-package for managing access on files and folders.<br>
            To edit the permissions on an archive element, the archive package implements an ACL-editor available in the "Attributes"-window.
            Right-click on an archive element and click the "Attributes"-item of the contextmenu
            or select an element and click on the "Attributes"-button in the toolbar, then navigate to
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Attributes.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="ACL">
        <h3>Managing permissions on files and folders</h3>
        <p>
            To manage permissions on files and folders, the archive package integrates an ACL-editor in the "Permissions"-tab of the "Attributes"-window.
            To open the "Attributes"-window, simply right-click on an archive element and click the "Attributes"-item of the contextmenu
            or select an element and click on the "Attributes"-button in the toolbar.
        </p>
        <p>
            The "Permissions"-tab will only be displayed if the current user is a member of a group with the "ReadAccessControlList" granted.
        </p>
        <p>
            The archive uses AccessControlLists from the "Security"-package for managing access on files and folders.<br>
            To edit the permissions on an archive element, the archive package implements an ACL-editor available in the "Attributes"-window.
            Right-click on an archive element and click the "Attributes"-item of the contextmenu
            or select an element and click on the "Attributes"-button in the toolbar, then navigate to
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "ACL.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="SystemFolder">
        <h4>System folder</h4>
        <p>
            The archive creates while installation a folder called "System" where package related files should be stored in;
            as used for example by the <a href="<?= Functions::URL("Documentation", "Package", "Events") ?>">Events</a>-
            or <a href="<?= Functions::URL("Documentation", "Package", "Machines") ?>">Machines</a>-packages.<br>
            This folder's ID is hardcoded to the value of the
            <code class="Inline">\Modules\<?= Code::Class("Archive") ?>::<?= Code::Const("System") ?></code>-constant,
            cannot be deleted
            and is only visible to the "System"-user and member of the "Administration"-group by default.
        </p>
            <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
                <img src="<?= Functions::Image("Documentation","Packages", "Archive", "SystemFolder.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
            </aside>
    </section>
    <section id="Search">
        <h4>Search</h4>
        <p>
            The archive package registers a search filter for performing a "LIKE"-search on the "Name"-column of the <code class="Inline"><?= Code::Const("Archive") ?>.<?= Code::Field("Elements") ?></code>-table.
            File results can be directly viewed by clicking on the search result in the result list on the left side;
            double-clicking will load the archive module and open the file in a new window.<br>

        </p>
            <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
                <img src="<?= Functions::Image("Documentation","Packages", "Search", "Search.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
            </aside>
    </section>
</article>