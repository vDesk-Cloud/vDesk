<?php use vDesk\Documentation\Code;
use vDesk\Pages\Functions; ?>
<article>
    <header>
        <h2>
            Configuration
        </h2>
        <p>
            This document describes the logic behind the global event system and how to dispatch and listen on events.<br>
            The functionality of the event system is provided by the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Events") ?>">Events</a>-package.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Introduction">Introduction</a></li>
            <li><a href="#Installation">Installation</a></li>
            <li>
                <a href="#Local">Local Configuration</a>
                <ul class="Topics">
                    <li><a href="#LocalClient">Client</a></li>
                    <li><a href="#LocalServer">Server</a></li>
                </ul>
            </li>
            <li>
                <a href="#Remote">Remote Configuration</a>
                <ul class="Topics">
                    <li><a href="#RemoteClient">Client</a></li>
                    <li><a href="#RemoteServer">Server</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <section id="Introduction">
        <h3>Configuration</h3>
        <p>
            Configuration
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Configuration", "Configuration.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="Installation">
        <h4>Installation</h4>
        <p>
            The configuration package doesn't require any user input while installation.
        </p>
    </section>
    <section id="Local">
        <h3>Local</h3>
        <p>
            Local
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Navigation.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="LocalClient">
        <h4>Client</h4>
        <p>
            Client
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Upload.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="LocalServer">
        <h4>Server</h4>
        <p>
            Server
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
        <h4>Custom viewers</h4>
        <p>
            The archive provides an API for registering JavaScript-classes as custom viewer controls.<br>
            To be recognized as a custom viewer, the class has to meet the following requirements:

            Files can be viewed by double clicking on them, right-clicking on them and clicking on the "Open"-item of the contextmenu and
            by selecting them and pressing the "Enter"-key or clicking on the "Open"-button of the toolbar.
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
            Editing files is only possible by right-clicking on the target file and clicking on the "Edit"-item of the contextmenu.<br>
            This item is only available if any editor-plugin which supports the file type has been registered
            and if the current user has "Write"-permissions on the file.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Edit.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="CustomEditor">
        <h4>Custom editors</h4>
        <p>
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