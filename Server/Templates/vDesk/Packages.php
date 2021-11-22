<?php use vDesk\Pages\Functions; ?>
<section class="Packages">
    <header>
        <h2>Packages</h2>
        <p class="Description">
            vDesk comes with a variety of preselected packages composed in the standard release.
        <br>
            For further information about packages and bundling resources, consider reading the <a
                    href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", "CustomReleases") ?>">Custom releases</a>-tutorial.
        </p>
        <h3>Current Packages</h3>
        <ul class="Packages Core">
            <li class="Title">Core</li>
            <li><a href="#Archive">Archive</a></a></li>
            <li><a href="#MetaInformation">MetaInformation</a></li>
            <li><a href="#Pinboard">Pinboard</a></li>
            <li><a href="#Calendar">Calendar</a></li>
            <li><a href="#Contacts">Contacts</a></li>
            <li><a href="#Messenger">Messenger</a></li>
            <li><a href="#Colors">Colors</a></li>
            <li><a href="#Locale">Locale</a></li>
            <li><a href="#Security">Security</a></li>
            <li><a href="#Configuration">Configuration</a></li>
            <li><a href="#Search">Search</a></li>
            <li><a href="#Console">Console</a></li>
            <li><a href="#Packages">Packages</a></li>
            <li><a href="#Setup">Setup</a></li>
            <li><a href="#Updates">Updates</a></li>
            <li><a href="#Events">Events</a></li>
            <li><a href="#Modules">Modules</a></li>
            <li><a href="#DataProvider">DataProvider</a></li>
            <li><a href="#vDesk">vDesk</a></li>
        </ul>
        <ul class="Packages Optional">
            <li class="Title">Optional</li>
            <li><a href="#Pages">Pages</a></a></li>
            <li><a href="#Homepage">Homepage</a></a></li>
            <li><a href="#Documentation">Documentation</a></a></li>
            <li><a href="#UpdateHost">UpdateHost</a></li>
            <li><a href="#Machines">Machines</a></li>
            <li><a href="#Tasks">Tasks</a></li>
        </ul>
        <div style="clear: both"></div>
    </header>
    <section>
        <article class="Package" id="Archive">
            <header>
                <h3>Archive</h3>
                <p>The Archive package provides a folder based virtual filesystem for archiving and editing files.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Events">Events</a></li>
                    <li><a href="#Locale">Locale</a></li>
                    <li><a href="#Security">Security</a></li>
                    <li><a href="#Search">Search</a></li>
                    <li><a href="https://webodf.org/" target="_blank">WebODF.js</a> (bundled)</li>
                </ul>
                <h4>Configuration</h4>
                <ul class="Configuration">
                    <li><span title="Recommended" class="Recommended">✓</span> max_input_time = -1</li>
                    <li><span title="Recommended" class="Recommended">✓</span> post_max_size = 0</li>
                    <li><span title="Recommended" class="Recommended">✓</span> upload_max_filzesize >= 1G</li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Archive.phar") ?>" download>Archive.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/ArchiveOverview.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Access Control List based sharing of files and folders between users and groups</li>
                    <li class="Feature">Folder based navigation through the Archive supporting keyboard controls</li>
                    <li class="Feature">Contextmenu sensitive selection enabling operations on multiple elements</li>
                </ul>
                <ul class="Features">
                    <li class="Feature">Parallel file uploads keeping track of the upload progress</li>
                    <li class="Feature">Search filter for quickly finding files and folders and conveniently displaying the contents of a file or navigating to a folder</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/ArchiveSearch.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="MetaInformation">
            <header>
                <h3>MetaInformation</h3>
                <p>
                    The MetaInformation package provides an index for datasets of metadata describing files and directories of the Archive with an interface for quickly searching
                    elements with matching metadata.
                </p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Archive">Archive</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "MetaInformation.phar") ?>" download>MetaInformation.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/MetaInformationDataSet.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Indexing files and folders with custom meta data</li>
                    <li class="Feature">Defining custom masks describing the schema of datasets</li>
                    <li class="Feature">Searching for specific values of datasets in different searchmodes</li>
                </ul>
            </section>
        </article>
        <article class="Package" id="Pinboard">
            <header>
                <h3>Pinboard</h3>
                <p>The Pinboard package provides a pinboard to attach frequently used files and folders or create and organize text notes.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Archive">Archive</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Pinboard.phar") ?>" download>Pinboard.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <ul class="Features">
                    <li class="Feature">Creating notes in different colors and sizes</li>
                    <li class="Feature">Attaching files and folders of the Archive for fast access</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Pinboard.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Calendar">
            <header>
                <h3>Calendar</h3>
                <p>The Calendar package provides a calendar module for organization of events.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Events">Events</a></li>
                    <li><a href="#Locale">Locale</a></li>
                    <li><a href="#Security">Security</a></li>
                    <li><a href="#Search">Search</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Calendar.phar") ?>" download>Calendar.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/CalendarMonthView.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Decade-, year-, month- and dayview</li>
                    <li class="Feature">Creation of events with custom highlight colors</li>
                </ul>
                <ul class="Features">
                    <li class="Feature">Access Control List based visibility of events</li>
                    <li class="Feature">Drag & drop supported organization of events</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/CalendarDayView.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Contacts">
            <header>
                <h3>Contacts</h3>
                <p>The Contacts package provides an address management module for private and business contacts.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Events">Events</a></li>
                    <li><a href="#Locale">Locale</a></li>
                    <li><a href="#Security">Security</a></li>
                    <li><a href="#Search">Search</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Contacts.phar") ?>" download>Contacts.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Contacts.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Organization of private and business contacts</li>
                    <li class="Feature">Access Control List based visibility of private contacts</li>
                    <li class="Feature">Searchfilter for searching for private and business contacts</li>
                </ul>
            </section>
        </article>
        <article class="Package" id="Messenger">
            <header>
                <h3>Messenger</h3>
                <p>The Messenger package provides a user and group based messenger.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Events">Events</a></li>
                    <li><a href="#Locale">Locale</a></li>
                    <li><a href="#Security">Security</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Messenger.phar") ?>" download>Messenger.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <ul class="Features">
                    <li class="Feature">Private chats</li>
                    <li class="Feature">Group chats</li>
                    <li class="Feature">(planned) Custom chatrooms</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Messenger.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Colors">
            <header>
                <h3>Colors</h3>
                <p>The Colors package provides an interface for customizing the colors and fonts of the client.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Configuration">Configuration</a></li>
                    <li><a href="#Locale">Locale</a></li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Colors.phar") ?>" download>Colors.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Colors.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Coloring everything (<a href="<?= Functions::URL("vDesk", "Page", "RoadMap#Colors") ?>">except icons</a>) of the client</li>
                    <li class="Feature">Multi-user support for individual color presets</li>
                </ul>
            </section>
        </article>
        <article class="Package" id="Locale">
            <header>
                <h3>Locale</h3>
                <p>The Locale package provides system wide translations and delivers a system for maintaining language packs.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Modules">Modules</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Locale.phar") ?>" download>Locale.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <ul class="Features">
                    <li class="Feature">System wide translation provider</li>
                    <li class="Feature">Custom package API for maintaining language packs</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Locale.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Security">
            <header>
                <h3>Security</h3>
                <p>The Security Package implements a security system managed through Access Control Lists and user groups.</p>
                <h4>License</h4>
                <p>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Configuration">Configuration</a></li>
                    <li><a href="#Events">Events</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/SecurityACL.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Access Control List based management of security critical components</li>
                    <li class="Feature">Drag & drop supported management of permissions</li>
                </ul>
                <ul class="Features">
                    <li class="Feature">Creation of user groups with fine-grained adjustable permissions</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/SecurityGroups.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Configuration">
            <header>
                <h3>Configuration</h3>
                <p>The Configuration package provides a system wide interface for a typesafe key-value storage of configuration settings and multiuser client side configuration</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Locale">Locale</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Configuration.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Systemwide typesafe key-value-storage system configuration</li>
                    <li class="Feature">Multi-user support for individual client side configuration settings</li>
                </ul>
            </section>
        </article>
        <article class="Package" id="Search">
            <header>
                <h3>Search</h3>
                <p>The Search package provides an interface for searching entities of compatible packages.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Modules">Modules</a></li>
                    <li><a href="#Locale">Locale</a></li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Search.phar") ?>" download>Search.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <ul class="Features">
                    <li class="Feature">Extensible filter API for searching specific components</li>
                    <li class="Feature">Extensible API for custom searches</li>
                    <li class="Feature">Preview of search results</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Search.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Console">
            <header>
                <h3>Console</h3>
                <p>The Console package provides a client side terminal window to perform administrative tasks.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Security">Security</a></li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Console.phar") ?>" download>Console.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Console.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Asynchronous execution of commands</li>
                    <li class="Feature">Extensible API for custom console commands</li>
                    <li class="Feature">Commandname autocomplete</li>
                    <li class="Feature">Executing commands in a different user context</li>
                </ul>
            </section>
        </article>
        <article class="Package" id="Packages">
            <header>
                <h3>Packages</h3>
                <p>The Packages package provides the package system vDesk is based on.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Events">Events</a></li>
                    <li><a href="#Locale">Locale</a></li>
                    <li><a href="#Security">Security</a></li>
                </ul>
                <h4>Configuration</h4>
                <ul class="Configuration">
                    <li><span title="Recommended" class="Recommended">✓</span> phar.readonly = 0</li>
                </ul>
            </header>
            <hr>
            <section class="Description">
                <ul class="Features">
                    <li class="Feature">(Un-)Installing feature packages</li>
                    <li class="Feature">Creation of custom packages</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Packages.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Updates">
            <header>
                <h3>Updates</h3>
                <p>The Updates package provides functionality for creating custom updates aswell as fetching and installing updates from remote hosts or local files.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Packages">Packages</a></li>
                </ul>
                <h4>Configuration</h4>
                <ul class="Configuration">
                    <li><span title="Recommended" class="Recommended">✓</span> phar.readonly = 0</li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Enabled sockets-extension</li>
                </ul>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Updates.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Downloading and installing updates from a configurable list of remote hosts</li>
                    <li class="Feature">Direct deployment of updates via file upload</li>
                    <li class="Feature">Creation of custom updates</li>
                </ul>
            </section>
        </article>
        <article class="Package" id="Setup">
            <header>
                <h3>Setup</h3>
                <p>The Setup package provides the functionality for creating and installing setups.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Packages">Packages</a></li>
                </ul>
                <h4>Configuration</h4>
                <ul class="Configuration">
                    <li><span title="Recommended" class="Recommended">✓</span> phar.readonly = 0</li>
                </ul>
            </header>
            <hr>
            <section class="Description">
                <ul class="Features">
                    <li class="Feature">Creating custom setups bundled with specific packages</li>
                    <li class="Feature">Installing setups</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("vDesk", "Installation.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Events">
            <header>
                <h3>Events</h3>
                <p>The Events package provides a system wide event system capable of dispatching public and private events.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Modules">Modules</a></li>
                    <li><a href="#Archive">Archive</a> <a href="<?= Functions::URL("vDesk", "Page", "RoadMap#Events") ?>">(will be made a "soft dependency")</a></li>
                </ul>
                <h4>Configuration</h4>
                <ul class="Configuration">
                    <li><span title="Recommended" class="Recommended">✓</span> max_execution_time = 0</li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
            </header>
        </article>
        <article class="Package" id="Modules">
            <header>
                <h3>Modules</h3>
                <p>The Modules core package provides a module system and input parameter validation.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#DataProvider">DataProvider</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
            </header>
        </article>
        <article class="Package" id="DataProvider">
            <header>
                <h3>DataProvider</h3>
                <p>The DataProvider core package provides an abstract injection safe database access layer for MySQL, Microsoft SQL Server and PostgreSQL.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#vDesk">vDesk</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
            </header>
        </article>
        <article class="Package" id="vDesk">
            <header>
                <h3>vDesk</h3>
                <p>The vDesk core package provides the underlying framework.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> PHP >= 8.0</li>
                    <li><span title="Required" class="Required">⚠</span> MySQL >= 5.6 compatible SQL server</li>
                    <li><span title="Required" class="Required">⚠</span> Webserver (Apache 2.4 recommended)</li>
                    <li><span title="Required" class="Required">⚠</span> ECMAScript 2020 compatible browser</li>
                    <li><span title="Required" class="Required">⚠</span> Enabled mysqli-extension</li>
                </ul>
            </header>
        </article>
    </section>
    <hr>
    <section>
        <article class="Package" id="Pages">
            <header>
                <h3>Pages</h3>
                <p>The Pages package provides a simple MVC framework for creating websites.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Configuration">Configuration</a></li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Pages.phar") ?>" download>Pages.phar</a></p>
            </header>
        </article>
        <article class="Package" id="Homepage">
            <header>
                <h3>Homepage</h3>
                <p>The Homepage package provides this website.</p>
                <h4>License</h4>
                <p>MIT</p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Pages">Pages</a></li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Homepage.phar") ?>" download>Homepage.phar</a></p>
            </header>
        </article>
        <article class="Package" id="Documentation">
            <header>
                <h3>Documentation</h3>
                <p>The Documentation package provides the <a href="<?= Functions::URL("Documentation", "Index") ?>">Documentation</a> website.</p>
                <h4>License</h4>
                <p>MIT</p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Pages">Pages</a></li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Documentation.phar") ?>" download>Documentation.phar</a></p>
            </header>
        </article>
        <article class="Package" id="UpdateHost">
            <header>
                <h3>UpdateHost</h3>
                <p>The UpdateHost package provides a server for delivering and hosting of (custom)updates.</p>
                <h4>License</h4>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Updates">Updates</a></li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> Separate database</li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "UpdateHost.phar") ?>" download>UpdateHost.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/UpdateHost.png") ?>">
                </aside>
                <ul class="Features">
                    <li class="Feature">Hosting of custom updates</li>
                </ul>
            </section>
        </article>
        <article class="Package" id="Machines">
            <header>
                <h3>Machines</h3>
                <p>The Machines package provides an OS agnostic process management.</p>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Configuration</h4>
                <ul class="Configuration">
                    <li><span title="Required" class="Required">⚠</span> max_execution_time = 0</li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> PHP >= 8.0 compiled with --enable-shmop</li>
                </ul>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Archive">Archive</a></li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Machines.phar") ?>" download>Machines.phar</a></p>
            </header>
            <hr>
            <section class="Description">
                <ul class="Features">
                    <li class="Feature">OS agnostic process management</li>
                    <li class="Feature">Lightweight object oriented interface for custom process classes</li>
                    <li class="Feature">Suspendable and resumable process instances.</li>
                </ul>
                <aside onclick="this.classList.toggle('Fullscreen')">
                    <img src="<?= Functions::Image("Packages/Machines.png") ?>">
                </aside>
            </section>
        </article>
        <article class="Package" id="Tasks">
            <header>
                <h3>Tasks</h3>
                <p>The Machines package provides an OS agnostic process control.</p>
                <p><a target="_blank" href="https://directory.fsf.org/wiki/License:MS-PL">Ms-PL</a></p>
                <h4>Configuration</h4>
                <ul class="Configuration">
                    <li><span title="Required" class="Required">⚠</span> max_execution_time = 0</li>
                </ul>
                <h4>Requirements</h4>
                <ul class="Requirements">
                    <li><span title="Required" class="Required">⚠</span> PHP >= 8.0 compiled with --enable-shmop</li>
                </ul>
                <h4>Dependencies</h4>
                <ul class="Dependencies">
                    <li><a href="#Machines">Machines</a></li>
                </ul>
                <h4>Download</h4>
                <p><a href="<?= Functions::URL("Downloads", "Tasks.phar") ?>" download>Tasks.phar</a></p>
            </header>
        </article>
    </section>
</section>