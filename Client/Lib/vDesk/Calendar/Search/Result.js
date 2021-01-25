"use strict";
/**
 * Initializes a new instance of the SearchResult class.
 * @class Represents a searchresult for events of a previous executed search.
 * @param {Object} Result.
 * @implements {vDesk.Search.IResult}
 * @memberOf vDesk.Calendar.Search
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
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


    /*Object.defineProperty(this, "Open", {
     enumerable: true,
     value: function () {
     var oModule = vDesk.WorkSpace.Find(Modules.Archive);
     vDesk.WorkSpace.Load(oModule);
     oModule.GoToID(_oEvent.ID);
     }
     });*/

};
vDesk.Calendar.Search.Result.Implements(vDesk.Search.IResult);
vDesk.Search.Results.Event = vDesk.Calendar.Search.Result;