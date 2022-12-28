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
            <li><a href="#Attributes">Attributes</a></li>
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
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Navigation.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
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
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Upload.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
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
    </section>
    <section id="View">
        <h3>Viewing files</h3>
        <p>
            Files can be viewed by double clicking on them, right-clicking on them and clicking on the "Open"-item of the contextmenu and
            by selecting them and pressing the "Enter"-button or clicking on the "Open"-button of the toolbar.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "View.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="CustomViewer">
        <h3>Custom viewers</h3>
        <p>
            The archive provides an API for registering JavaScript-classes as custom viewer controls.<br>
            To be recognized as a custom viewer, the class has to meet the following requirements:

            Files can be viewed by double clicking on them, right-clicking on them and clicking on the "Open"-item of the contextmenu and
            by selecting them and pressing the "Enter"-button or clicking on the "Open"-button of the toolbar.
        </p>
        <ul>
            <li>Located in the <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Archive") ?>.<?= Code::Field("Element") ?>.<?= Code::Field("View") ?></code>-namespace</li>
            <li>Implement a <code class="Inline"><?= Code::Field("Control") ?></code>-property holding the underlying DOM-Node of the custom viewer.</li>
            <li>Implement a static <code class="Inline"><?= Code::Field("Extensions") ?></code>-property holding the supported file-types of the custom viewer.</li>
        </ul>
        <h5><u>Definition of an example HTML document viewer</u></h5>
        <pre><code><?= Code\Language::JS ?>
<?= Code::Variable("vDesk") ?>.<?= Code::Field("Archive") ?>.<?= Code::Field("Element") ?>.<?= Code::Field("View") ?>.<?= Code::Class("HTML") ?> = <?= Code::Function ?>(<?= Code::Variable("Element") ?>) {

    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::This ?>, <?= Code::String("\"Control\"") ?>, {<?= Code::Field("get") ?>: () => <?= Code::Const("Control") ?>})<?= Code::Delimiter ?>


    <?= Code::Constant ?> <?= Code::Const("Control") ?> = <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"div\"") ?>)<?= Code::Delimiter ?>

    <?= Code::Const("Control") ?>.<?= Code::Field("className") ?> = <?= Code::String("\"HTML\"") ?><?= Code::Delimiter ?>


    <?= Code::Variable("vDesk") ?>.<?= Code::Field("Connection") ?>.<?= Code::Function("Send") ?>(
        <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Modules") ?>.<?= Code::Class("Command") ?>({
            <?= Code::Field("Module") ?>:     <?= Code::String("\"Archive\"") ?>,
            <?= Code::Field("Command") ?>:    <?= Code::String("\"Download\"") ?>,
            <?= Code::Field("Parameters") ?>: {<?= Code::Field("ID") ?>: <?= Code::Variable("Element") ?>.<?= Code::Field("ID") ?>},
            <?= Code::Field("Ticket") ?>:     <?= Code::Variable("vDesk") ?>.<?= Code::Field("Security") ?>.<?= Code::Class("User") ?>.<?= Code::Field("Current") ?>.<?= Code::Field("Ticket") ?>

        }),
        <?= Code::Variable("Buffer") ?> => {
            <?= Code::Constant ?> <?= Code::Const("Reader") ?> = <?= Code::New ?> <?= Code::Class("FileReader") ?>()<?= Code::Delimiter ?>

            <?= Code::Const("Reader") ?>.<?= Code::Field("onload") ?> = () => <?= Code::Const("Control") ?>.<?= Code::Function("appendChild") ?>(<?= Code::New ?> <?= Code::Class("DomParser") ?>().<?= Code::Function("parseFromString") ?>(<?= Code::Const("Reader") ?>.<?= Code::Field("result") ?>, <?= Code::String("\"text/html\"") ?>))<?= Code::Delimiter ?>

            <?= Code::Const("Reader") ?>.<?= Code::Function("readAsText") ?>(<?= Code::New ?> <?= Code::Class("Blob") ?>([<?= Code::Variable("Buffer") ?>], {<?= Code::Field("type") ?>: <?= Code::String("\"text/plain\"") ?>}))<?= Code::Delimiter ?>

        },
        <?= Code::True ?>

    )<?= Code::Delimiter ?>

}<?= Code::Delimiter ?>


<?= Code::Variable("vDesk") ?>.<?= Code::Field("Archive") ?>.<?= Code::Field("Element") ?>.<?= Code::Field("View") ?>.<?= Code::Class("HTML") ?>.<?= Code::Field("Extensions") ?> = [
    <?= Code::String("\"html\"") ?>,
    <?= Code::String("\"xhtml\"") ?>

]<?= Code::Delimiter ?>

</code></pre>
    </section>
    <section id="Copy">
        <h3>Copy files</h3>
        <p>
            Elements can be copied by selecting and right-clicking on them and then clicking on the "Copy"-item of the contextmenu
            or clicking on the "Copy"-button and clicking on the "Paste"-button in the target folder
            or right-clicking on a target folder
            and clicking on the "Paste"-item of the contextmenu.<br>
            The same action can be achieved by selecting elements and pressing "CTRL+C" and "CTRL+V" in the target folder.<br>
            This operation will require the current user to have "read"-access on the target elements and "write"-access on the destination folder.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Copy.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="Move">
        <h3>Move files and folders</h3>
        <p>
            Elements can be moved by selecting and dragging them onto a target folder
            or by clicking on the "Cut"-button while selected and clicking on the "Paste"-button in the target folder
            or right-clicking on a target folder
            and clicking on the "Paste"-item of the contextmenu.<br>
            The same action can be achieved by selecting elements and pressing "CTRL+X" and "CTRL+V" in the target folder.<br>
            This operation will require the current user to have "write"-access on the target elements and destination folder.
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
            The attributes window contains general information like the size or owner of an element, a permission editor and
            an editor for managing <a href="<?= Functions::URL("Documentation", "Package", "MetaInformation#DataSet") ?>">DataSets</a>.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Attributes.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
        <p>
            The Attributes window can be extended via registering a custom control in the
            <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Archive") ?>.<?= Code::Field("Attributes") ?></code>-namespace.
            vDesk.Archive.Attributes
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "AttributesWindow.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="ACL">
        <h3>Managing permissions on files and folders</h3>
        <p>
            The archive relies on <a href="<?= Functions::URL("Documentation", "Package", "Security#ACL") ?>">AccessControlLists</a> from the <a href="<?= Functions::URL("Documentation", "Package", "Security") ?>">Security</a>-package for managing access on files and folders.<br>
            To edit the permissions on an archive element, the archive package integrates an ACL-editor in the "Attributes"-window.
            Right-click on an archive element and click the "Attributes"-item of the contextmenu
            or select an element and click on the "Attributes"-button in the toolbar, then navigate to the "Permissions"-tab.
        </p>
        <p>
            The "Permissions"-tab will only be displayed if the current user is a member of a group with the "ReadAccessControlList"-permission granted.<br>
            Newly created folders or uploaded files will inherit their ACL from their according parent element.
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