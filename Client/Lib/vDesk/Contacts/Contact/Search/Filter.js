"use strict";
/**
 * Searchfilter for searching contacts by a similar surname.
 * @type Object
 * @memberOf vDesk.Contacts.Contact.Search
 */
vDesk.Contacts.Contact.Search.Filter = {
    Module: "Contacts",
    Name: "Contact",
    get Icon() {return vDesk.Visual.Icons.Contacts.Module;},
    get Title() {return vDesk.Locale["Contacts"]["Module"]}
};
vDesk.Search.Filters.Contacts = vDesk.Contacts.Contact.Search.Filter;