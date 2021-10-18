<?php
use vDesk\Documentation\Code;
use vDesk\Documentation\Code\Language;
use vDesk\Pages\Functions;
?>
<article>
    <header>
        <h2>Custom search-filters and -results</h2>
        <p>
            vDesk provides several methods to search for specific objects and /or data in the system, like files and folders in the archive, events in the calendar or metadata of archive
            entries.
        </p>
        <p>
            The 'generic'-search view of the Search module provides a way to define custom search-filters and results which define the pattern of the search-operation and the format and
            presentation of the result if any.
        </p>
    </header>
    <section>
        <h3>
            Custom search-filters
        </h3>
        <p>
            A custom search-filter extends the search-module by the capability of performing a search-operation against a specified serverside module which provides its own kind of data as a
            resultset. So it is possible to search the archive for example for files and folders and/or search the Contact module for a specific contact or business-contact.
        </p>
        <h5>Custom search filter provided via plugin API</h5>
        <p style="text-align: center">
            <img src="<?= Functions::Image("Documentation/SearchFilter.png") ?>">
        </p>
    </section>
    <section>
        <h4>
            Format
        </h4>
        <p>
            Search filters are plain JavaScript-objects that must implement the following members:
        </p>
        <table>
            <tr>
                <th>Property</th>
                <th>Type</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>Module</td>
                <td>String</td>
                <td>Defines the name of the server-side module, against the search-operation will be performed.</td>
            </tr>
            <tr>
                <td>Name</td>
                <td>String</td>
                <td>Defines the name of the filter for identification on the server. Depending on the implementation of the <code class="Inline"><?= Code::Class("ISearch") ?>::<?= Code::Function("Search") ?>()</code>-method of the according module, the value can
                    be an empty string.
                </td>
            </tr>
            <tr>
                <td>Icon</td>
                <td>String</td>
                <td>Defines the icon that will be displayed on the ToolBarItem for the search-filter.</td>
            </tr>
            <tr>
                <td>Title</td>
                <td>String</td>
                <td>Defines the title that will be displayed on the ToolBarItem for the search-filter.</td>
            </tr>
        </table>
        <h5>Search-filter registration</h5>
        <p>To register a custom search-filter it must be declared as a member of the <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Search") ?>.<?= Code::Field("Filters") ?></code>-namespace.</p>
        <br>
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
    <section>
        <h4>Custom search-results</h4>
        <p>A custom search-result defines the behaviour and/or presentation of a specific kind of data which has been retrieved by a search operation from the server.<br>
            To register a search-result it must follow the </p>
        <p></p>
        <h5>Custom search-results provided via plugin API</h5>
        <p style="text-align: center">
            <img src="<?= Functions::Image("Documentation/SearchResult.png") ?>">
        </p>
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
</article>