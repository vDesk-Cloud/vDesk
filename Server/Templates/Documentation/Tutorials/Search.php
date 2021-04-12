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
            Custom searchfilters
        </h3>
        <p>
            A custom searchfilter extends the searchmodule by the capability of performing a searchoperation against a specified serverside module which provides its own kind of data as a
            resultset. So it is possible to search the archive for example for files and folders and/or search the contactmodule for a specific contact or business-contact.
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
        <table>
            <tr>
                <th>Key</th>
                <th>Value</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>Module</td>
                <td>String</td>
                <td>Defines the name of the server-side module, against the searchoperation will be performed.</td>
            </tr>
            <tr>
                <td>Name</td>
                <td>[String]</td>
                <td>Defines the name of the filter for identification on the server. Depending on the implementation of the ISearch->Search() method of the according module, the value can
                    be an empty string.
                </td>
            </tr>
            <tr>
                <td>Icon</td>
                <td>String</td>
                <td>Defines the icon that will be displayed on the toolbaritem for the searchfilter.</td>
            </tr>
            <tr>
                <td>Title</td>
                <td>String</td>
                <td>Defines the title that will be displayed on the toolbaritem for the searchfilter.</td>
            </tr>
        </table>
        <h5>Searchfilter registration</h5>
        <p>To register a custom search-filter it must be declared as a member of the <code class="Inline"><?= Code::Class("vDesk") ?>.Search.Filters</code>-namespace.</p>
        <br>
        <h5><u>Definition of an example searchfilter</u></h5>
        <pre><code><?= Language::JS ?>
<?= Code::BlockComment("\"use strict\"") ?><?= Code::Delimiter ?>
        
        
<?= Code::BlockComment("/**
 * Searchfilter for searching contacts by a similar Surname.
 * @type Object
 * @memberOf vDesk.Contacts
 */") ?>
        
<?= Code::Class("vDesk") ?>.Contacts.Contact.Search.<?= Code::Field("Filter") ?> = {
    
    <?= Code::Comment("//Target module to perform the search-operation against.") ?>
        
    <?= Code::Variable("Module") ?>: <?= Code::String("\"Contacts\"") ?>,
    
    <?= Code::Comment("//Tells the specified module to search only for contacts. (Persons)") ?>
        
    <?= Code::Variable("Name") ?>: <?= Code::String("\"Contact\"") ?>,
    
    <?= Code::Comment("//The icon to show in the toolbar.") ?>
        
    <?= Code::Variable("Icon") ?>: <?= Code::Class("vDesk") ?>.Visual.Icons.Security.<?= Code::Field("User") ?>,
    
    <?= Code::Comment("//The title of the ToolBar Item for the search-filter.") ?>
        
    <?= Code::Variable("Title") ?>: <?= Code::Class("vDesk") ?>.<?= Code::Field("Locale") ?>[<?= Code::String("\"Contacts\"") ?>][<?= Code::String("\"Contact\"") ?>]
}<?= Code::Delimiter ?>
        
        
<?= Code::Comment("//Registration as a filter.") ?>

<?= Code::Class("vDesk") ?>.Search.Filters.Contacts = <?= Code::Class("vDesk") ?>.Contacts.Contact.Search.Filter<?= Code::Delimiter ?>
</code></pre>
    </section>
    <section>
        <h4>Custom searchresults</h4>
        <p>A custom searchresult defines the behaviour and/or presentation of a specific kind of data which has been retrieved by a search operation from the server.<br>
            To register a searchresult it must follow the </p>
        <p></p>
        <h5>Custom search results provided via plugin API</h5>
        <p style="text-align: center">
            <img src="<?= Functions::Image("Documentation/SearchResult.png") ?>">
        </p>
        <h5><u>Definition of an example searchresult</u></h5>
        <pre><code><?= Language::JS ?>
<?= Code::BlockComment("\"use strict\"") ?><?= Code::Delimiter ?>
        
        
<?= Code::BlockComment("/**
 * @class Custom search result.
 * @param {SearchResult} Result The data of the returned search-result.
 * @implements {vDesk.Search.IResult}
 */") ?>
        
<?= Code::Class("CustomSearchResult") ?> = <?= Code::Function ?> (<?= Code::Variable("Result") ?>) {
    
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperties") ?>(<?= Code::This ?>, {
        <?= Code::Variable("Viewer") ?>: {
            <?= Code::Function("get") ?>: () => <?= Code::New ?> <?= Code::Class("ViewerForCustomResult") ?>(<?= Code::Variable("Result") ?>.<?= Code::Field("Data") ?>)
        },
        <?= Code::Variable("Icon") ?>: {
            <?= Code::Variable("value") ?>: <?= Code::Class("vDesk") ?>.Visual.Icons.CustomPackage.<?= Code::Field("CustomIcon") ?>
        
        },
        <?= Code::Variable("Name") ?>: {
            <?= Code::Variable("value") ?>: <?= Code::Variable("Result") ?>.<?= Code::Field("Name") ?> || <?= Code::String("\"CustomResult\"") ?>
        
        },
        <?= Code::Function("Open") ?>: {
            <?= Code::Variable("value") ?>: () => {
            <?= Code::Comment("//Process result...") ?>
        
            }
        }
    })<?= Code::Delimiter ?>
    
}<?= Code::Delimiter ?>
        
<?= Code::Comment("//Implementation of interface.") ?>

<?= Code::Class("CustomSearchResult") ?>.<?= Code::Function("Implements") ?>(<?= Code::Class("vDesk") ?>.Search.<?= Code::Class("IResult") ?>)<?= Code::Delimiter ?>


<?= Code::Comment("//Registration as a custom search-result.") ?>

<?= Code::Class("vDesk") ?>.Search.Results.<?= Code::Field("CustomSearchResult") ?> = <?= Code::Class("CustomSearchResult") ?><?= Code::Delimiter ?>
</code></pre>
    </section>
</article>