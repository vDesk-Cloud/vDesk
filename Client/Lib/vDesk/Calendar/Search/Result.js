"use strict";
/**
 * Initializes a new instance of the SearchResult class.
 * @class Represents a searchresult for events of a previous executed search.
 * @param {Object} Result.
 * @implements {vDesk.Search.IResult}
 * @memberOf vDesk.Calendar.Search
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Calendar
 */
vDesk.Calendar.Search.Result = function Result(Result) {

    Object.defineProperties(this, {
        Viewer: {
            enumerable: true,
            get:        function() {
                return (new vDesk.Calendar.Event.Viewer(vDesk.Calendar.Event.FromDataView(Result.Data))).Control;
            }
        },
        Icon:   {
            enumerable: true,
            value:      "calendar"
        },
        Name:   {
            enumerable: true,
            value:      Result.Name
        },
        Type:   {
            enumerable: true,
            value:      Result.Type
        }
    });

};
vDesk.Calendar.Search.Result.Implements(vDesk.Search.IResult);
vDesk.Search.Results.Event = vDesk.Calendar.Search.Result;