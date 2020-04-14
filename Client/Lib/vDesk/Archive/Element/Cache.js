"use strict";
/**
 * Initializes a new instance of the Cache class.
 * @class Provides functionality for fetching Elements from the server and caching them.
 * @property {Array<vDesk.Archive.Element>} Elements Gets or sets the Elements of the Cache.
 * @memberOf vDesk.Archive.Element
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Archive.Element.Cache = function Cache() {

    /**
     * The Elements of the Cache.
     * @type {Array.<vDesk.Archive.Element>}
     */
    let Elements = [];

    Object.defineProperty(this, "Elements", {
        enumerable: true,
        get:        () => Elements,
        set:        Value => {
            Ensure.Property(Value, Array, "Elements");
            Elements = value;
        }
    });

    /**
     * Performs a recursive treewalk and gets all children of an Element.
     * @param {vDesk.Archive.Element} Element The element to search for its children.
     * @return {Array<vDesk.Archive.Element>} The found elements.
     */
    const GetChildren = function(Element) {
        const Children = [];
        const fnGetChildren = function(Element) {
            Elements.filter(CacheElement => CacheElement.Parent.ID === Element.ID).forEach(TempElement => {
                fnGetChildren(TempElement);
                Children.push(TempElement);
            });
        };
        fnGetChildren(Element);
        return Children;
    };

    /**
     * Returns the index of an Element in the cache.
     * @param {vDesk.Archive.Element} Element The element to search the index of.
     * @return {Number} The index of the Element to search, -1 if no Element was found.
     */
    this.FindIndex = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        return Elements.findIndex(CachedElement => CachedElement.ID !== null && CachedElement.ID === Element.ID);
    };

    /**
     * Adds one or more Elements to the Cache.
     * @param {vDesk.Archive.Element} Elements The Elements to add.
     */
    this.Add = function(...Elements) {
        Elements.forEach(Element => {
            Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
            //Check if the Element already has been cached.
            const Index = this.Elements.findIndex(CachedElement => CachedElement.ID === Element.ID);
            if(~Index) {
                this.Elements[Index] = Element;
            } else {
                this.Elements.push(Element);
            }
        });
    };

    /**
     * Gets an Element with a specified ID from the Cache.
     * @param {Number} ID The ID of the Element to get.
     * @param {Boolean} [Update=false] Flag indicating whether to update the Element if it already exists in the Cache.
     * @return {vDesk.Archive.Element|null} The cached Element with the specified ID; otherwise, null.
     */
    this.GetElement = function(ID, Update = false) {
        Ensure.Parameter(ID, Type.Number, "ID");
        Ensure.Parameter(Update, Type.Boolean, "Update");

        const CachedElement = this.Find(ID);
        if(CachedElement !== null && !Update) {
            return CachedElement;
        }
        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Archive",
                    Command:    "GetElement",
                    Parameters: {ID: ID},
                    Ticket:     vDesk.User.Ticket
                }
            )
        );
        if(Response.Status) {
            const Element = vDesk.Archive.Element.FromDataView(Response.Data);
            Element.Parent = Elements.find(Parent => Parent.ID === Element.Parent.ID) || Element.Parent;
            this.Add(Element);
            return Element;
        }
        return null;
    };

    /**
     * Fetches an Element  with a specified ID from the Cache.
     * @param {Number} ID The ID of the Element to fetch.
     * @param {Function} Callback The callback to execute when the Element has been fetched from the server.
     * @param {Boolean} [Update=false] Flag indicating whether to update the Element if it already exists in the Cache.
     */
    this.FetchElement = function(ID, Callback, Update = false) {
        Ensure.Parameter(ID, Type.Number, "ID");
        Ensure.Parameter(Callback, Type.Function, "Callback");
        Ensure.Parameter(Update, Type.Boolean, "Update");

        const CachedElement = this.Find(ID);
        if(CachedElement !== null && !Update) {
            Callback(CachedElement);
        } else {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Archive",
                        Command:    "GetElement",
                        Parameters: {ID: ID},
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        const Element = vDesk.Archive.Element.FromDataView(Response.Data);
                        Element.Parent = Elements.find(Parent => Parent.ID === Element.Parent.ID) || Element.Parent;
                        this.Add(Element);
                        Callback(Element);
                    } else {
                        Callback(null);
                    }
                }
            );
        }
    };

    /**
     * Gets all child Elements of a specified parent Element from the Cache.
     * @param {vDesk.Archive.Element}  Parent The parent Element to get the children of.
     * @param {Boolean} [Update=false] Flag indicating whether to update already cached Elements.
     * @return {Array<vDesk.Archive.Element>} An array containing the cached Elements of the specified parent Element.
     */
    this.GetElements = function(Parent, Update = false) {
        Ensure.Parameter(Parent, vDesk.Archive.Element, "Parent");
        Ensure.Parameter(Update, Type.Boolean, "Update");

        //Checks if Elements have been already cached.
        let CachedElements = Elements.filter(Element => Element.Parent.ID === Parent);
        if(CachedElements.length > 0 && !Update) {
            return CachedElements;
        }
        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Archive",
                    Command:    "GetElements",
                    Parameters: {ID: Parent},
                    Ticket:     vDesk.User.Ticket
                }
            )
        );
        if(Response.Status) {
            const Elements = Response.Data.map(vDesk.Archive.Element.FromDataView);
            this.Add(...Elements);
            return Elements;
        }
        return [];
    };

    /**
     * Fetches all child Elements of a specified Parent.
     * @param {vDesk.Archive.Element} Parent The parent Element to fetch the children of.
     * @param {Function} Callback The callback to execute when the Elements have been fetched from the server.
     * @param {Boolean} [Update=false] Flag indicating whether to update already cached Elements.
     */
    this.FetchElements = function(Parent, Callback, Update = false) {
        Ensure.Parameter(Parent, vDesk.Archive.Element, "Parent");
        Ensure.Parameter(Callback, Type.Function, "Callback");
        Ensure.Parameter(Update, Type.Boolean, "Update");

        //Checks if Elements have been already cached.
        let CachedElements = Elements.filter(Element => Element.Parent.ID === Parent);
        if(CachedElements.length > 0 && !Update) {
            Callback(CachedElements);
        } else {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Archive",
                        Command:    "GetElements",
                        Parameters: {ID: Parent.ID},
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        const Elements = Response.Data.map(vDesk.Archive.Element.FromDataView);
                        Elements.forEach(Element => Element.Parent = Parent);
                        this.Add(...Elements);
                        Callback(Elements);
                    }
                }
            );
        }
    };

    /**
     * Removes an Element from the Cache.
     * @param {vDesk.Archive.Element} Element The Element to remove.
     */
    this.Remove = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

        let Index = Elements.indexOf(Element);
        Index = (~Index) ? Index : this.FindIndex(Element);

        //Check if the element is in the cache.
        if(~Index) {
            //Remove the element from the cache.
            Elements.splice(Index, 1);
            //Remove any children from the index.
            GetChildren(Element).forEach(Child => Elements.splice(this.FindIndex(Child), 1));
        }
    };

    /**
     * Finds an Element within the Cache.
     * @param {Number} ID The ID of the Element to find.
     * @return {vDesk.Archive.Element|null} The found Element; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "ID");
        return Elements.find(Element => Element.ID === ID) || null;
    };

};
