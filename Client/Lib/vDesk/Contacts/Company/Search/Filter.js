"use strict";
/**
 * Searchfilter for searching Companies by name.
 * @type Object
 * @memberOf vDesk.Contacts.Company.Search
 */
vDesk.Contacts.Company.Search.Filter = {
    Module: "Contacts",
    Name: "Company",
    get Icon() {return vDesk.Visual.Icons.Contacts.Company;},
    get Title() {return vDesk.Locale["Contacts"]["Companies"]}
};
vDesk.Search.Filters.Companies = vDesk.Contacts.Company.Search.Filter;