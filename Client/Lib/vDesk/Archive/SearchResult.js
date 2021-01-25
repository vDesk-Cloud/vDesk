"use strict";
/**
 * Initializes a new instance of the SearchResult class.
 * @class Represents a SearchResult for Elements of a previous executed search.
 * @param {Result} Result The data of the SearchResult.
 * @implements vDesk.Search.IResult
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Archive.SearchResult = function SearchResult(Result) {

    /**
     * The Viewer of the SearchResult.
     * @type {null|vDesk.Archive.Element.Viewer}
     */
    let Viewer = null;

    Object.defineProperties(this, {
        Viewer: {
            enumerable: true,
            get:        () => {
                if(Viewer === null) {
                    Viewer = new vDesk.Archive.Element.Viewer(Element);
                }
                return Viewer.Control
            }
        },
        Icon:   {
            enumerable: true,
            get:        () => vDesk.Visual.Icons.Archive[Result.Data?.Extension ?? "Folder"]
        },
        Name:   {
            enumerable: true,
            value:      Result.Name
        },
        Type:   {
            enumerable: true,
            value:      Result.Type
        },
        Open:   {
            enumerable: true,
            value:      function() {
                const Module = vDesk.Modules.Archive;
                vDesk.WorkSpace.Module = Module;
                Module.GoToID(Element.ID);
            }
        }
    });

    /**
     * The element of the SearchResult.
     * @type {vDesk.Archive.Element}
     */
    const Element = new vDesk.Archive.Element();
    Element.ID = Result.Data.ID;
    Element.Type = Result.Data.Type;
    Element.Extension = Result.Data.Extension;

};
vDesk.Archive.SearchResult.Implements(vDesk.Search.IResult);

//Register SearchResult.
vDesk.Search.Results.Element = vDesk.Archive.SearchResult;