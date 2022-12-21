<?php
use vDesk\Documentation\Code;
use vDesk\Documentation\Code\Language;
use vDesk\Pages\Functions;
?>
<article>
    <header>
        <h2>Search</h2>
        <p>
            This document contains the documentation of vDesk's <a href="<?= Functions::URL("vDesk", "Page", "Packages#Search") ?>">Search</a>-package
            and guides for using the search and implementing custom search-filters, -results and how to register completely custom search-interfaces.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Introduction">Introduction</a></li>
            <li><a href="#Filters">Custom search filters</a></li>
            <li><a href="#Results">Custom search results</a></li>
            <li><a href="#Search">Custom searches</a></li>
        </ul>
    </header>
    <section id="Introduction">
        <h3>
            Introduction
        </h3>
        <p>
            vDesk provides several methods to search for specific objects or data in the system, like files and folders in the archive,
            events in the calendar or metadata of archive entries.
        </p>
        <p>
            The search is available on the client via clicking on the "Search"-button in the module-list on the left border.<br>
            The search-module is dissected into a generic search with filters and an area for completely custom search-controls, switched through by the first toolbar.

        </p>
        <p>
            The 'generic'-search view of the Search module provides a way to define custom search-filters and results which define the pattern of the search-operation and the format and
            presentation of the result if any.
        </p>
        <p>
            A search will automatically be triggered when 3 or more characters have been entered in the search-value textbox.
        </p>
        <p>
            After submitting a search value, the system passes the value and the name of the selected filter to the filter-module's <code class="Inline"><?= Code::Class("IModule") ?>::<?= Code::Function("Search") ?>()</code>-method.
        </p>
        <p>
            Every currently available and searchable module uses the SQL "LIKE"-clause for comparison, so wildcards can be used in searches;
            however the underlying <a href="<?= Functions::URL("Documentation", "Package", "DataProvider") ?>">DataProvider</a>-package doesn't
        </p>
        <p>
            After the server returned a set of results, these will be displayed in the result list on the left side, which can be sorted by name and type asc- and descending.<br>
            Clicking on a result will load the underlying entity and display it in a registered custom viewer control, while double-clicking it will open the entity in it's according module
            (Navigating through the archive for example if clicked on a "folder"-result).
        </p>
        <h5>Client search-module</h5>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation", "Packages", "Search", "Search.png") ?>">
        </aside>
    </section>
    <section id="Filters">
        <h3>
            Custom search-filters
        </h3>
        <p>
            The generic search provides an API for defining custom filters to search certain server-modules which provide their own kind of data as a
            result set.<br>
            So it is possible to search the archive for example for files and folders and/or search the Contact module for a specific contact or business-contact.
        </p>
        <p>
            After submitting a search value, the system passes the value and the name of the selected filter to the filter-module's <code class="Inline"><?= Code::Class("IModule") ?>::<?= Code::Function("Search") ?>()</code>-method.
        </p>
        <p>
            Every currently available and searchable module uses the SQL "LIKE"-clause for comparison, so wildcards can be used in searches;
            however the underlying <a href="<?= Functions::URL("Documentation", "Package", "DataProvider") ?>">DataProvider</a>-package doesn't
        </p>
        <h5>Custom search filter provided via plugin API</h5>
        <p style="text-align: center">

        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation", "Packages", "Search", "Filter.png") ?>">
        </aside>
    </section>
    <section>
        <h4>
            Format
        </h4>
        <p>
            Search filters are plain JavaScript-objects that must meet the following requirements:
        </p>
        <ul>
            <li>Located in the <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Field("Filters") ?></code>-namespace</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Module") ?></code>-property holding the name of the target server-module.</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Name") ?></code>-property holding the name of the filter for identification.</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Icon") ?></code>-property holding an ObjectURL of an icon for the filter.</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Title") ?></code>-property holding a display-name of the filter.</li>
        </ul>
        <h5><u>Definition of an example search-filter</u></h5>
        <pre><code><?= Language::JS ?>
<?= Code::BlockComment("\"use strict\"") ?><?= Code::Delimiter ?>
        
        
<?= Code::BlockComment("/**
 * Searchfilter for searching contacts by a similar Surname.
 * @type Object
 * @memberOf vDesk.Contacts
 */") ?>
        
<?= Code::Variable("vDesk") ?>.<?= Code::Field("Contacts") ?>.<?= Code::Field("Contact") ?>.<?= Code::Field("Search") ?>.<?= Code::Class("Filter") ?> = {
    
    <?= Code::Comment("//Target module to perform the search-operation against.") ?>
        
    <?= Code::Field("Module") ?>: <?= Code::String("\"Contacts\"") ?>,
    
    <?= Code::Comment("//Tells the specified module to search only for contacts. (Persons)") ?>
        
    <?= Code::Field("Name") ?>: <?= Code::String("\"Contact\"") ?>,
    
    <?= Code::Comment("//The icon to show in the toolbar.") ?>
        
    <?= Code::Field("Icon") ?>: <?= Code::Variable("vDesk") ?>.<?= Code::Field("Visual") ?>.<?= Code::Field("Icons") ?>.<?= Code::Field("Security") ?>.<?= Code::Field("User") ?>,
    
    <?= Code::Comment("//The title of the ToolBar Item for the search-filter.") ?>
        
    <?= Code::Field("Title") ?>: <?= Code::Variable("vDesk") ?>.<?= Code::Class("Locale") ?>[<?= Code::String("\"Contacts\"") ?>][<?= Code::String("\"Contact\"") ?>]
}<?= Code::Delimiter ?>
        
        
<?= Code::Comment("//Registration as a filter.") ?>

<?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Field("Filters") ?>.<?= Code::Field("Contacts") ?> = <?= Code::Variable("vDesk") ?>.<?= Code::Field("Contacts") ?>.<?= Code::Field("Contact") ?>.<?= Code::Field("Search") ?>.<?= Code::Class("Filter") ?><?= Code::Delimiter ?>
</code></pre>
    </section>
    <section id="Results">
        <h4>Custom search-results</h4>
        <p>
            A custom search-result defines the behaviour and/or presentation of a specific kind of data which has been retrieved by a search operation from the server.<br>
        </p>
        <h5>Custom search-results provided via plugin API</h5>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation", "Packages", "Search", "Result.png") ?>">
        </aside>
    </section>
    <section id="ResultFormat">
        <h4>
            Format
        </h4>
        <p>
            Search results are small JavaScript-classes that must meet the following requirements:
        </p>
        <ul>
            <li>Located in the <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Field("Results") ?></code>-namespace</li>
            <li>Implement the <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Class("IResult") ?></code>-interface.</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Name") ?></code>-property holding the name of the filter for identification.</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Icon") ?></code>-property holding an ObjectURL of an icon for the filter.</li>
        </ul>
        <h5><u>Definition of an example search-result</u></h5>
        <pre><code><?= Language::JS ?>
<?= Code::BlockComment("\"use strict\"") ?><?= Code::Delimiter ?>
        
        
<?= Code::BlockComment("/**
 * @class Custom search result.
 * @param {SearchResult} Result The data of the returned search-result.
 * @implements {vDesk.Search.IResult}
 */") ?>
        
<?= Code::Class("CustomSearchResult") ?> = <?= Code::Function ?> (<?= Code::Variable("Result") ?>) {
    
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperties") ?>(<?= Code::This ?>, {
        <?= Code::Field("Viewer") ?>: {
            <?= Code::Function("get") ?>: () => <?= Code::New ?> <?= Code::Class("ViewerForCustomResult") ?>(<?= Code::Variable("Result") ?>.<?= Code::Field("Data") ?>)
        },
        <?= Code::Field("Icon") ?>: {
            <?= Code::Field("value") ?>: <?= Code::Class("vDesk") ?>.<?= Code::Field("Visual") ?>.<?= Code::Field("Icons") ?>.<?= Code::Field("CustomPackage") ?>.<?= Code::Field("CustomIcon") ?>
        
        },
        <?= Code::Field("Name") ?>: {
            <?= Code::Field("value") ?>: <?= Code::Variable("Result") ?>.<?= Code::Field("Name") ?> ?? <?= Code::String("\"CustomResult\"") ?>
        
        },
        <?= Code::Function("Open") ?>: {
            <?= Code::Field("value") ?>: () => {
            <?= Code::Comment("//Process result...") ?>
        
            }
        }
    })<?= Code::Delimiter ?>
    
}<?= Code::Delimiter ?>
        
<?= Code::Comment("//Implementation of interface.") ?>

<?= Code::Class("CustomSearchResult") ?>.<?= Code::Function("Implements") ?>(<?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Class("IResult") ?>)<?= Code::Delimiter ?>


<?= Code::Comment("//Registration as a custom search-result.") ?>

<?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Field("Results") ?>.<?= Code::Field("CustomSearchResult") ?> = <?= Code::Class("CustomSearchResult") ?><?= Code::Delimiter ?>
</code></pre>
    </section>
    <section id="Search">
        <h3>Custom search</h3>
        <p>
            The search client-module provides an API for registering classes as completely custom search-controls at a centralized point - as for example is used by the MetaInformation-package.<br>
            Custom search-controls are listed in the "Search"-toolbar-group next to the generic search's item and can by toggled by clicking on their toolbar item.
        </p>
        <p>
            Custom search-controls must meet the following requirements:
        </p>
        <ul>
            <li>Located in the <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Field("Custom") ?></code>-namespace</li>
            <li>Implement the <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Class("ICustomSearch") ?></code>-interface.</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Control") ?></code>-property holding the underlying DOM-Node of the custom search.</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Title") ?></code>-property holding the display-name of the custom search.</li>
            <li>Implement a public <code class="Inline"><?= Code::Field("Icon") ?></code>-property holding an ObjectURL of the icon of the custom search.</li>
            <li>
                Implement a public <code class="Inline"><?= Code::Field("ToolBarGroups") ?></code>-property holding an optional array of
                <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Field("ToolBar") ?>.<?= Code::Class("Group") ?></code>-instances
                to merge with the search-module's toolbar groups.
            </li>
        </ul>
        <h5>Custom search-controls provided via plugin API</h5>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation", "Packages", "Search", "Custom.png") ?>">
        </aside>
    </section>
</article>