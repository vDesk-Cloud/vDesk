<?php

use vDesk\Documentation\Code;
use vDesk\Pages\Functions;

?>
<article class="Packages">
    <header>
        <h2>Packages, updates and setups</h2>
        <p>
            This document describes the specifications of packages, updates and setups and how to create custom versions.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Versioning">Versioning</a></li>
            <li>
                <a href="#Packages">Packages</a>
                <ul class="Topics">
                    <li><a href="#PackageManifest">Manifest</a></li>
                    <li><a href="#PackagePreparationCleanup">Preparation&cleanup</a></li>
                    <li><a href="#CustomPackages">Custom packages</a></li>
                    <li><a href="#CustomInstallers">Custom installers</a></li>
                    <li><a href="#PackageCreation">Creating packages</a></li>
                </ul>
            </li>
            <li>
                <a href="#Updates">Updates</a>
                <ul class="Topics">
                    <li><a href="#UpdateManifest">Manifest</a></li>
                    <li><a href="#UpdateCreation">Creating updates</a></li>
                </ul>
            </li>
            <li>
                <a href="#Setups">Setups</a>
                <ul class="Topics">
                    <li>
                        <a href="#SetupCreation">Creating setups</a>
                        <ul class="Topics">
                            <li><a href="#Exclude">Excluding packages</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </header>
    <section id="Versioning">
        <h3>Versioning</h3>
        <p>
            vDesk uses a version-number format that is compatible with PHP's built in
            <a target="_blank" href="https://www.php.net/manual/de/function.version-compare.php">\version_compare()</a>-function.<br>
        </p>
        <p>
            The version number of updates are split into following parts:
        </p>
        <h4>First digit</h4>
        <p>
            Represents the latest major release.<br>
            This number increases if the package has been overhauled or adds a greater set of features.
        </p>
        <h4>Second digit</h4>
        <p>
            Represents the latest feature(s) and/or deprecation(s).<br>
            This number increases if smaller files and methods have been added or removed.
        </p>
        <h4>Third digit</h4>
        <p>
            Represents the latest bugg-fix(es).<br>
            This number increases for every fixed bugg by one step.
        </p>
    </section>
    <section id="Packages">
        <h3 id="Packages">Packages</h3>
        <p>
            Packages are PHAR-archives bundled with the resources defined by a package <a href="#PackageManifest">manifest</a> class
            named after the value of its <code class="Inline"><?= Code::Const("Name") ?></code>-constant,
            with a stub that returns an instance of the manifest class upon inclusion.
        </p>
        <p>
            A usual package consists of a package manifest class file placed in the <code class="Inline"><?= Code::Console("/Server/Lib/vDesk/Packages") ?></code> directory while any
            client- or server-side resources are stored in certain directories.
        </p>
        <pre><code>--- <?= Code::BlockComment("Package.phar") ?>

    |
      --- <?= Code::BlockComment("Client") ?> <?= Code::Comment("//Client-side resources") ?>

          |
            --- <?= Code::BlockComment("Design") ?> <?= Code::Comment("//Client-side CSS stylesheets") ?>

                |
                  --- <?= Code::BlockComment("Package.css") ?> <?= Code::Comment("//Client-side package CSS stylesheet") ?>

          |
            --- <?= Code::BlockComment("Lib") ?> <?= Code::Comment("//Client-side library files") ?>

                |
                  --- <?= Code::BlockComment("Package") ?> <?= Code::Comment("//Client-side package library") ?>

          |
            --- <?= Code::BlockComment("Modules") ?> <?= Code::Comment("//Client-side modules") ?>

                |
                  --- <?= Code::BlockComment("Package.js") ?> <?= Code::Comment("//Client-side package module") ?>

    |
      --- <?= Code::BlockComment("Server") ?> <?= Code::Comment("//Server-side resources") ?>

          |
            --- <?= Code::BlockComment("Lib") ?> <?= Code::Comment("//Server-side library files") ?>

                |
                  --- <?= Code::BlockComment("Package") ?> <?= Code::Comment("//Server-side package library") ?>

                |
                  --- <?= Code::BlockComment("vDesk") ?> <?= Code::Comment("//vDesk library") ?>

                      |
                        --- <?= Code::BlockComment("Packages") ?> <?= Code::Comment("//Package manifest class files") ?>

                            |
                              --- <?= Code::BlockComment("Package.php") ?> <?= Code::Comment("//Package manifest class file") ?>

          |
            --- <?= Code::BlockComment("Modules") ?> <?= Code::Comment("//Server-side modules") ?>

                |
                  --- <?= Code::BlockComment("Package.php") ?> <?= Code::Comment("//Server-side package module") ?>
    </code></pre>
        <aside class="Note">
            <h4>Note</h4>
            <p>
                To keep complexity low, the loadorder of JavaScript resources is determined by their namespace hierarchy.<br>
                However, depending on the underlying filesystem, the loadorder of resources within a namespace, may differ across operating systems.
            </p>
            <p>
                A correct loadorder can <u>only</u> be guaranteed across dependant packages and within the second and third package resource hierarchy layer, as long as .
            </p>
        </aside>
    </section>
    <section id="PackageManifest">
        <h4>Manifest</h4>
        <p>
            Packages are defined by package manifest classes which have to meet the following conditions:
        </p>
        <ul>
            <li>Located in the <code class="Inline">\vDesk\Packages</code>-namespace.</li>
            <li>Inheriting from the <code class="Inline">\vDesk\Packages\<?= Code::Class("Package") ?></code>-class.</li>
            <li>Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Name") ?></code>-constant containing the name of the package.</li>
            <li>
                Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Version") ?></code>-constant
                containing a version number that is compatible with PHP's built in
                <a target="_blank" href="https://www.php.net/manual/de/function.version-compare.php">\version_compare()</a>-function.
            </li>
            <li>
                If the Package requires any dependencies:
                Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Dependencies") ?></code>-constant
                containing an associative array of packages and minimum versions.
            </li>
            <li>Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Vendor") ?></code>-constant containing the name of author/company of the package.</li>
            <li>Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Description") ?></code>-constant containing the description of the package.</li>
            <li>(Optionally)Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("License") ?></code>-constant containing the license of the package(defaults to Ms-PL).</li>
            <li>(Optionally)Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("LicenseText") ?></code>-constant containing the text of the license of the package.</li>
            <li>(Optionally)Declaring a <code class="Inline"><?= Code::Public ?> <?= Code::Const("Files") ?></code>-constant containing the resources of the package.</li>
            <li>Implementing a <code class="Inline"><?= Code::Public ?> <?= Code::Static ?> <?= Code::Function("Install") ?>()</code>-method.</li>
            <li>Implementing a <code class="Inline"><?= Code::Public ?> <?= Code::Static ?> <?= Code::Function("Uninstall") ?>()</code>-method.</li>
            <li>
                (Optionally)Implementing a <code class="Inline"><?= Code::Public ?> <?= Code::Static ?> <?= Code::Function("PreInstall") ?>()</code>-method
                if the package requires any preparations.
            </li>
            <li>
                (Optionally)Implementing a <code class="Inline"><?= Code::Public ?> <?= Code::Static ?> <?= Code::Function("PostInstall") ?>()</code>-method
                if the package requires any cleanup operations.
            </li>
        </ul>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Packages<?= Code::Delimiter ?>


<?= Code::Keyword("final") ?> <?= Code::ClassDeclaration ?> <?= Code::Class("CustomPackage") ?> <?= Code::Extends ?> <?= Code::Class("Package") ?> {
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"ExamplePackage\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Version") ?> = <?= Code::String("\"1.0.0\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Vendor") ?> = <?= Code::String("\"Author/company &lt;mail@example.com&gt;\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Description") ?> = <?= Code::String("\"This package provides super cool features!\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Dependencies") ?> = [
        <?= Code::String("\"Archive\"") ?>  => <?= Code::String("\"1.0.0\"") ?>,
        <?= Code::String("\"Calendar\"") ?> => <?= Code::String("\"1.0.0\"") ?>,
        ...
    ]<?= Code::Delimiter ?>
    
    
    <?= Code::Comment("//Optional license. Defaults to Ms-PL.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("License") ?> = <?= Code::String("\"MIT\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Comment("//Optional license text. Defaults to a hint on the about dialog.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("LicenseText") ?> = <?= Code::String("\"...\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Comment("//Basic file/directory structure of packages.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Files") ?> = [
        
        <?= Code::Self ?>::<?= Code::Const("Client") ?> => [
        
            <?= Code::Comment("//Client-side CSS stylesheets.") ?>
        
            <?= Code::Self ?>::<?= Code::Const("Design") ?> => [
                <?= Code::String("\"Namespace/ExamplePackage/Example.css\"") ?>,
                <?= Code::String("\"Namespace/ExamplePackage/Examples\"") ?>
        
            ],
        
            <?= Code::Comment("//Client-side JavaScript library files.") ?>
        
            <?= Code::Self ?>::<?= Code::Const("Lib") ?> => [
                <?= Code::String("\"Namespace/ExamplePackage/Example.js\"") ?>,
                <?= Code::String("\"Namespace/ExamplePackage/ExampleLib\"") ?>
            
            ],
        
            <?= Code::Comment("//Client-side JavaScript library files.") ?>
        
            <?= Code::Self ?>::<?= Code::Const("Modules") ?> => [
                <?= Code::String("\"ExampleModule.js\"") ?>
            
            ]
        ],
        <?= Code::Self ?>::<?= Code::Const("Server") ?> => [
        
            <?= Code::Comment("//Server-side PHP library files.") ?>
        
            <?= Code::Self ?>::<?= Code::Const("Lib") ?> => [
                <?= Code::String("\"Namespace/ExamplePackage/Example.php\"") ?>,
                <?= Code::String("\"Namespace/ExamplePackage/ExampleLib\"") ?>
            
            ],
        
            <?= Code::Comment("//Server-side PHP modules.") ?>
        
            <?= Code::Self ?>::<?= Code::Const("Modules") ?> => [
                <?= Code::String("\"ExampleModule.php\"") ?>
            
            ]
        ]
    ]<?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Install") ?>(\<?= Code::Class("Phar") ?> <?= Code::Variable("\$Phar") ?>, <?= Code::Keyword("string") ?> <?= Code::Variable("\$Path") ?>): <?= Code::Void ?> {
        
        <?= Code::Comment("//Deploy files.") ?>
        
        <?= Code::Self ?>::<?= Code::Function("Deploy") ?>(<?= Code::Variable("\$Phar") ?>, <?= Code::Variable("\$Path") ?>)<?= Code::Delimiter ?>
        
        
        <?= Code::Comment("//Alter database, create permissions, ...") ?>
        
        
    }
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Uninstall") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Path") ?>): <?= Code::Void ?> {
        
        <?= Code::Comment("//Remove files.") ?>
        
        <?= Code::Self ?>::<?= Code::Function("Undeploy") ?>()<?= Code::Delimiter ?>
        
        
        <?= Code::Comment("//Revert everything of \"Install\"-method...") ?>
        
        
    }
    
}</code></pre>
    </section>
    <section id="PackagePreparationCleanup">
        <h4>Preparation&Cleanup</h4>
        <p>
            Packages requiring user input, have to check system requirements or preparing environments,<br>
            must implement a public static "PreInstall"-method, that is being called, before any package or module has been installed.
        </p>
        <p>
            Packages requiring a fully installed system or perform cleanup tasks,<br>
            must implement a public static "PostInstall"-method, that is being called, after every package and its modules has been installed.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Packages<?= Code::Delimiter ?>


<?= Code::Keyword("final") ?> <?= Code::ClassDeclaration ?> <?= Code::Class("CustomPackage") ?> <?= Code::Extends ?> <?= Code::Class("Package") ?> {

    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"ExamplePackage\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("PreInstall") ?>(\<?= Code::Class("Phar") ?> <?= Code::Variable("\$Phar") ?>, <?= Code::Keyword("string") ?> <?= Code::Variable("\$Path") ?>): <?= Code::Void ?> {
        
        <?= Code::Comment("//Where to install?") ?>
    
        \<?= Code::Function("readline") ?>(<?= Code::String("\"Installation target directory:\" ") ?>)<?= Code::Delimiter ?>
        
        
    }
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("PostInstall") ?>(\<?= Code::Class("Phar") ?> <?= Code::Variable("\$Phar") ?>, <?= Code::Keyword("string") ?> <?= Code::Variable("\$Path") ?>): <?= Code::Void ?> {
        
        <?= Code::Comment("//Cleanup temporary files, call modules..") ?>
        
        
    }
    
}</code></pre>
    </section>
    <section id="CustomPackages">
        <h4>Custom packages</h4>
        <p>
            The installer of vDesk comes with an API for providing installable custom packages.<br>
            While installation, the package gets passed to every "installer"-module, that has to care for itself for a compatible package type, in setup context; this applies to every
            bundled package.
        </p>
        <p>
            To be recognized as a custom package, the package manifest class must implement either one of the predefined interfaces oder provide a custom package interface.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Packages<?= Code::Delimiter ?>


<?= Code::Keyword("final") ?> <?= Code::ClassDeclaration ?> <?= Code::Class("CustomPackage") ?> <?= Code::Extends ?> <?= Code::Class("Package") ?> <?= Code::Implements ?> \vDesk\Locale\<?= Code::Class("IPackage") ?>, \vDesk\Events\<?= Code::Class("IPackage") ?> {
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"CustomPackage\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Description") ?> = <?= Code::String("\"Translations and EventListeners\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Dependencies") ?> = [
        <?= Code::String("\"Events\"") ?> => <?= Code::String("\"1.0.0\"") ?>,
        <?= Code::String("\"Locale\"") ?> => <?= Code::String("\"1.0.0\"") ?>
        
    ]<?= Code::Delimiter ?>
    
    
    <?= Code::Comment("//Translations.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Locale") ?> = [
        <?= Code::String("\"DE\"") ?> => [
            <?= Code::String("\"Hallo\"") ?> => [
                <?= Code::String("\"Welt\"") ?> => <?= Code::String("\"Hallo Welt!\"") ?>
        
            ]
        ],
        <?= Code::String("\"EN\"") ?> => [
            <?= Code::String("\"Hello\"") ?> => [
                <?= Code::String("\"World\"") ?> => <?= Code::String("\"Hello world!\"") ?>
        
            ]
        ]
    ]<?= Code::Delimiter ?>
    
    
    <?= Code::Comment("//EventListeners.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Events") ?> = [
        <?= Code::String("\"vDesk.Security.User.Deleted\"") ?> => <?= Code::String("\"Package/EventListeners/UserDeleted.php\"") ?>
        
    ]<?= Code::Delimiter ?>
    
    
    <?= Code::Comment("//EventListener files.") ?>
    
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Files") ?> = [
        <?= Code::Self ?>::<?= Code::Const("Server") ?> => [
            <?= Code::Self ?>::<?= Code::Const("Lib") ?> => [
                <?= Code::String("\"Package/EventListeners/UserDeleted.php\"") ?>
        
            ]
        ]
    ]<?= Code::Delimiter ?>
    
    
}</code></pre>
    </section>
    <section id="CustomInstallers">
        <h4>Custom installers</h4>
        <p>
            To install a custom package, there has to be at least one installed module that is capable of installing the package.<br>
            To be recognized as a potential installer, the module must implement the <code class="Inline">\vDesk\Packages\<?= Code::Class("IModule") ?></code>-interface<br> and provide an
            "Install"-method that processes the package.<br>
            To keep complexity of the package system as simple as possible, the installer has to check each passed package for a compatible package type and must resolve required constants
            before processing.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> Modules<?= Code::Delimiter ?>


<?= Code::Keyword("final") ?> <?= Code::ClassDeclaration ?> <?= Code::Class("CustomInstaller") ?> <?= Code::Extends ?> \vDesk\Modules\<?= Code::Class("Modules") ?> <?= Code::Implements ?> \vDesk\Packages\<?= Code::Class("IModule") ?> {
    
    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Install") ?>(\vDesk\Packages\<?= Code::Class("Package") ?> <?= Code::Variable("\$Package") ?>, \<?= Code::Class("Phar") ?> <?= Code::Variable("\$Phar") ?>, <?= Code::Keyword("string") ?> <?= Code::Variable("\$Path") ?>): <?= Code::Void ?> {
        
        <?= Code::If ?>(!<?= Code::Variable("\$Package") ?> <?= Code:: InstanceOf ?> \vDesk\Locale\<?= Code::Class("IPackage") ?>) {
            <?= Code::Return ?><?= Code::Delimiter ?>
        
        }
        
        <?= Code::Comment("//Install translations.") ?>
        
        <?= Code::ForEach ?>(<?= Code::Variable("\$Package") ?>::<?= Code::Const("Locale") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Locale") ?> => <?= Code::Variable("\$Domains") ?>) {
        
        }
        
    }
    
}</code></pre>
        <aside class="Note">
        <h4>Developer's note</h4>
        <p>
            You may wonder why every custom package-interface is empty and the required package-constants are resolved on runtime.<br>
            Unfortunately, PHP doesn't support multiple inheritance and interface-constants are immutable, at least i wanted to provide a consistent API.
        </p>
        <p>
            An alternative may enforcing "Files", "Locale", etc... to be static methods returning arrays, but for general information like name or license, we still have to stick to
            base classes.<br>
            My idea behind this decision was that every developer just has to create a simple package-class with a few constants saying:
        </p>
        <p class="Quote">
            <cite>
                "Hi, my name is ..., Made by ..., (I'm licensed under ...), I have these files and those translations, maybe some EventListeners...<br>
                In my 'Install'-method, I'm setting up a database and it's tables, extract my files and so on..."
            </cite>
        </p>
    </aside>
    </section>
    <section id="PackageCreation">
        <h4>Creating packages</h4>
        <p>
            The creation of packages requires the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Packages") ?>">Packages</a> and
            <a href="<?= Functions::URL("vDesk", "Page", "Packages#Console") ?>">Console</a>-packages
            to be installed (these are bundled by default in the official releases).<br>
            To create a single custom package press <code class="Inline">° / Shift + ^</code> to open the client side console
            and enter one of the following commands:
        </p>
        <pre><code><span class="Console">Call -M=Packages -C=Create -Package=Archive [-Path=C:\Users, -Compression=<?= \Phar::GZ ?>]
Call -Module=Packages -Command=Create -Package=Calendar [-Path=/home/user, -Compression=<?= \Phar::BZ2 ?>]</span></code></pre>
        <p>
            This will create a PHAR archive named like the specified package and bundled with the files and folders of the package at the optionally specified path on the server.<br>
            If the "Path"-parameter is omitted, the package file will be created in the system's "Server"-directory.
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
    <section id="UpdateManifest">
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
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>

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
    </section>
    <section id="UpdateCreation">
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
    <section id="Setups">
        <h3 id="Setups">Setups</h3>
        <p>
            Setups are executable PHAR-archives bundled with the resources defined by all bundled package <a href="#PackageManifest">manifest</a> classes
            named "Setup.phar"
            with a stub that calls the <code class="Inline">\Modules\<?= Code::Class("Setup") ?>::<?= Code::Function("Install") ?>()</code> method upon execution.
        </p>
    <pre><code>--- <?= Code::BlockComment("Setup.phar") ?>
        
        |
          --- <?= Code::BlockComment("Client") ?> <?= Code::Comment("//Client-side resources.") ?>
            
              |
                --- <?= Code::BlockComment("...") ?>
            
          --- <?= Code::BlockComment("Server") ?> <?= Code::Comment("//Server-side resources.") ?>
            
              |
                --- <?= Code::BlockComment("Lib") ?> <?= Code::Comment("//Server-side library files.") ?>
            
                    |
                      --- <?= Code::BlockComment("vDesk") ?> <?= Code::Comment("//vDesk library.") ?>
            
                          |
                            --- <?= Code::BlockComment("Packages") ?> <?= Code::Comment("//Package manifest class files.") ?>
            
              |
                --- <?= Code::BlockComment("Modules") ?> <?= Code::Comment("//Server-side modules.") ?>
    </code></pre>
    </section>
    <section id="SetupCreation">
        <h4>Creating setups</h4>
        <p>
            The creation of setups requires the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Setup") ?>">Setup</a> and
            <a href="<?= Functions::URL("vDesk", "Page", "Packages#Console") ?>">Console</a>-packages to be installed
            (these are bundled by default in the official releases).<br>
            To create a custom setup press <code class="Inline">° / Shift + ^</code> to open the client side console
            and enter one of the following commands:
        </p>
        <pre><code><span class="Console">Call -M=Setup -C=Create [-Path=E:\Development\Setups]
Call -Module=Setup -Command=Create [-Path=/var/www/htdocs/vDesk/Server]</span></code></pre>
        <p>
            This will create a "Setup.phar"-file bundled with all currently installed packages at the optionally specified path on the server.<br>
            If the "Path"-parameter is omitted, the setup file will be created in the system's "Server"-directory.
        </p>
    </section>
    <section id="Exclude">
        <h5>Excluding packages</h5>
        <p>
            To exclude certain packages, a comma separated list of packages can be specified that won't get bundled in the setup.
        </p>
        <pre><code><span class="Console">Call -M=Setup -C=Create [-Path=%TargetDir%, -Exclude=Pages, Homepage, Documentation, ...]
Call -Module=Setup -Command=Create [-Path=%TargetDir%, -Exclude=%A%, %B%, ...]</span></code></pre>
        <p>
            Alternatively, a JSON array of packages can be specified.
        </p>
        <pre><code><span class="Console">Call -M=Setup -C=Create -Exclude=["Pages", "Homepage", "Documentation", "Contacts", "Messenger"]</span></code></pre>

    </section>
</article>