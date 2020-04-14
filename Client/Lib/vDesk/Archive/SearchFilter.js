"use strict";
/**
 * SearchFilter for searching archive entries by a similar name.
 * @type Object
 * @memberOf vDesk.Archive
 */
vDesk.Archive.SearchFilter = {
    Module: "Archive",
    Name: "Element",
    get Icon() {return vDesk.Visual.Icons.Archive.Module;},
    get Title() {return vDesk.Locale["Archive"]["Module"];}
};
vDesk.Search.Filters.Archive = vDesk.Archive.SearchFilter;