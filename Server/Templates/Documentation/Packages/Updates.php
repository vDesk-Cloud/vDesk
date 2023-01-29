<?php

use vDesk\Documentation\Code;
use vDesk\Pages\Functions;

?>
<article class="Packages">
    <header>
        <h2>Updates</h2>
        <p>
            This document describes the update system of vDesk.<br>
            It contains technical specifications on installable updates and instructions on how to check for and install updates as well as querying custom update servers.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Search">Searching for updates</a></li>
            <li><a href="#Install">Installing updates</a></li>
            <li>
                <a href="#Updates">Updates</a>
                <ul class="Topics">
                    <li><a href="#Manifest">Manifest</a></li>
                    <li><a href="#Create">Creating updates</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <section>

    </section>
    <section id="Search">
        <h3>Searching for updates</h3>
        <p>
            To update a running installation, open the main menu by clicking in the top left corner on the button labeled "vDesk".<br>
            Open the "Administration"-dialog and navigate to the "Updates"-tab.
        </p>
        <p>
            By clicking on the "Search for Updates"-button, the updater will query all potential update servers registered in the local configuration file <code class="Inline">%InstallDir%/Server/Settings/Updates.php</code>.<br>
            As per default, the update endpoint located under "updates.vdesk.cloud" will be already registered in the configuration file.
        </p>

        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Updates", "Updates.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
    </section>
    <section id="Install">
        <h4>Installing updates</h4>
        <p>
            By clicking the "Install"-button, the selected update will then be downloaded and installed.<br>
            Afterwards, if the update has been successfully installed, the selected entry gets removed from the list of available updates.<br>
            Alternatively, updates can be uploaded and installed manually from local files via clicking on the "Deploy"-button and selecting any potential update files.
        </p>
    </section>
    <section id="Updates">
        <h3>Updates</h3>
        <p>
            Updates are PHAR-archives bundled with the resources defined by an update <a href="#UpdateManifest">manifest</a> class
            named after the value of its package's <code class="Inline"><?= Code::Const("Name") ?></code>-constant
            followed by the new version enclosed in square brackets,
            with a stub that returns a tuple of the update's update and package manifest classes upon inclusion.
        </p>
        <p>
            A usual update consists of an update manifest class file placed in the <code class="Inline"><?= Code::Console("/Server/Lib/vDesk/Updates") ?></code> directory<br>
            as well as an updated version of the target package manifest class file placed in the <code class="Inline"><?= Code::Console("/Server/Lib/vDesk/Packages") ?></code>
            directory<br>
            while any client- or server-side resources are stored in their specific directories.
        </p>
        <pre><code>--- <?= Code::BlockComment("Update.phar") ?>
        
    |
      --- <?= Code::BlockComment("Client") ?> <?= Code::Comment("//Updated client-side resources") ?>

          |
            --- <?= Code::BlockComment("Design") ?> <?= Code::Comment("//Updated client-side CSS stylesheets") ?>

                |
                  --- <?= Code::BlockComment("Package.css") ?> <?= Code::Comment("//Updated client-side package CSS stylesheet") ?>

          |
            --- <?= Code::BlockComment("Lib") ?> <?= Code::Comment("//Updated client-side library files") ?>

                |
                  --- <?= Code::BlockComment("Package") ?> <?= Code::Comment("//Updated client-side package library") ?>

          |
            --- <?= Code::BlockComment("Modules") ?> <?= Code::Comment("//Updated client-side modules") ?>

                |
                  --- <?= Code::BlockComment("Package.js") ?> <?= Code::Comment("//Updated client-side package module") ?>

    |
      --- <?= Code::BlockComment("Server") ?> <?= Code::Comment("//Updated server-side resources") ?>

          |
            --- <?= Code::BlockComment("Lib") ?> <?= Code::Comment("//Updated server-side library files") ?>

                |
                  --- <?= Code::BlockComment("Package") ?> <?= Code::Comment("//Updated server-side package library") ?>

                |
                  --- <?= Code::BlockComment("vDesk") ?> <?= Code::Comment("//vDesk library") ?>

                      |
                        --- <?= Code::BlockComment("Packages") ?> <?= Code::Comment("//Package manifest class files") ?>

                            |
                              --- <?= Code::BlockComment("Package.php") ?> <?= Code::Comment("//Updated target package manifest class file") ?>

                      |
                        --- <?= Code::BlockComment("Updates") ?> <?= Code::Comment("//Updated manifest class files") ?>

                            |
                              --- <?= Code::BlockComment("Update.php") ?> <?= Code::Comment("//Updated manifest class file") ?>

          |
            --- <?= Code::BlockComment("Modules") ?> <?= Code::Comment("//Updated server-side modules") ?>

                |
                  --- <?= Code::BlockComment("Package.php") ?> <?= Code::Comment("//Updated server-side package module") ?>
    </code></pre>
    </section>
    <section id="Manifest">
        <h4>Manifest</h4>
        <p>
            Updates are defined by manifest classes which have to meet the following conditions:
        </p>
        <ul>
            <li>Located in the <code class="Inline">\vDesk\Updates</code>-namespace.</li>
            <li>Inheriting from the <code class="Inline">\vDesk\Updates\<?= Code::Class("Update") ?></code>-class.</li>
            <li>Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Package") ?></code>-constant holding the class name of the updated package's manifest class.</li>
            <li>Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("RequiredVersion") ?></code>-constant holding a version number that represents the minimum required version of the target package to update.</li>
            <li>Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Description") ?></code>-constant holding the description of the update.</li>
            <li>(Optionally)Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Files") ?></code>-constant holding the resources to deploy/overwrite and/or delete of the update.</li>
            <li>Implementing a <code class="Inline"><?= Code::Public ?> <?= Code::Static ?> <?= Code::Function("Install") ?>()</code>-method.</li>
        </ul>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Example implementation of a custom Update manifest class</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(57) ?>
        <pre><code><?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Updates<?= Code::Delimiter ?>


<?= Code::Keyword("final") ?> <?= Code::ClassDeclaration ?> <?= Code::Class("CustomUpdate") ?> <?= Code::Extends ?> <?= Code::Class("Update") ?> {
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Package") ?> = \vDesk\Packages\<?= Code::Class("ExamplePackage") ?>::<?= Code::ClassDeclaration ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("RequiredVersion") ?> = <?= Code::String("\"1.0.0\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Description") ?> = <?= Code::String("\"This patch fixes some minor buggs!\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Files") ?> = [
        <?= Code::Comment("//Resources to add/update.") ?>
        
        <?= Code::Class("Update") ?>::<?= Code::Const("Deploy") ?> => [
            <?= Code::Class("Package") ?>::<?= Code::Const("Client") ?> => [
                <?= Code::Class("Package") ?>::<?= Code::Const("Design") ?> => [
                    <?= Code::String("\"Namespace/ExamplePackage/Example.css\"") ?>,
                    <?= Code::String("\"Namespace/ExamplePackage/New.css\"") ?>
            
                ]
            ],
            <?= Code::Class("Package") ?>::<?= Code::Const("Server") ?> => [
                <?= Code::Class("Package") ?>::<?= Code::Const("Lib") ?> => [
                    <?= Code::String("\"Namespace/ExamplePackage/Example.php\"") ?>
                
                ],
                <?= Code::Class("Package") ?>::<?= Code::Const("Modules") ?> => [
                    <?= Code::String("\"ExampleModule.php\"") ?>
                
                ]
            ],
        <?= Code::Comment("//Resources to delete.") ?>
        
        <?= Code::Class("Update") ?>::<?= Code::Const("Undeploy") ?> => [
            <?= Code::Class("Package") ?>::<?= Code::Const("Client") ?> => [
                <?= Code::Class("Package") ?>::<?= Code::Const("Design") ?> => [
                    <?= Code::String("\"Namespace/ExamplePackage/Old.css\"") ?>
        
                ]
            ],
            <?= Code::Class("Package") ?>::<?= Code::Const("Server") ?> => [
                <?= Code::Class("Package") ?>::<?= Code::Const("Lib") ?> => [
                    <?= Code::String("\"Namespace/ExamplePackage/ExampleLib\"") ?>
                
                ]
            ]
        ]
    ]<?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Install") ?>(\<?= Code::Class("Phar") ?> <?= Code::Variable("\$Phar") ?>, <?= Code::Keyword("string") ?> <?= Code::Variable("\$Path") ?>): <?= Code::Void ?> {
        
        <?= Code::Comment("//Remove outdated or unnecessary files.") ?>
        
        <?= Code::Self ?>::<?= Code::Function("Undeploy") ?>()<?= Code::Delimiter ?>
        
        
        <?= Code::Comment("//Alter database, permissions, ...") ?>
        
        
        <?= Code::Comment("//Deploy updated or new files.") ?>
        
        <?= Code::Self ?>::<?= Code::Function("Deploy") ?>(<?= Code::Variable("\$Phar") ?>, <?= Code::Variable("\$Path") ?>)<?= Code::Delimiter ?>
        
    }
    
}</code></pre>
        </aside>
    </section>
    <section id="Create">
        <h4>Creating updates</h4>
        <p>
            The creation of updates requires the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Updates") ?>">Updates</a> and
            <a href="<?= Functions::URL("vDesk", "Page", "Packages#Console") ?>">Console</a>-packages to be
            installed (these are bundled by default in the official releases).<br>
            To create a single custom update press <code class="Inline">° / Shift + ^</code> to open the client side console
            and enter one of the following commands:
        </p>
        <pre><code><span class="Console">Call -M=Updates -C=Create -Update=Archive [-Path=C:\Users, -Compression=<?= \Phar::GZ ?>]
Call -Module=Updates -Command=Create -Update=Calendar [-Path=/home/user, -Compression=<?= \Phar::BZ2 ?>]</span></code></pre>
        <p>
            This will create a PHAR archive named like the specified update and bundled with the files and folders of the update and its according package manifest file at the optionally specified path on
            the server.<br>
            If the "Path"-parameter is omitted, the update file will be created in the system's "Server"-directory.
        </p>
    </section>
</article>