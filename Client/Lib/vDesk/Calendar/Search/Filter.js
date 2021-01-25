"use strict";
/**
 * Searchfilter for searching calendar events by their title.
 * @type Object
 * @memberOf vDesk.Calendar.Search
 */
vDesk.Calendar.Search.Filter = {
    Module: "Calendar",
    Name:   "Event",
    get Icon() {
        return vDesk.Visual.Icons.Calendar.Module;
    },
    get Title() {
        return vDesk.Locale.Calendar.Module
    }
};
vDesk.Search.Filters.Calendar = vDesk.Calendar.Search.Filter;