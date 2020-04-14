"use strict";
/**
 * Initializes a new instance of the ElementCache class.
 * @class Provides functionality for fetching Elements from the server and caching them.
 * @property {Array<vDesk.Archive.Element>} Elements Gets or sets the Elements of the ElementCache.
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Archive.ElementCache = function ElementCache() {

    /**
     * The Elements of the ElementCache.
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
     * Fetches an Element from the cache by its ID.
     * If the Element doesn't already exist in the cache, it will get fetched from the server.
     * @param {Number} ID The ID of the Element to fetch.
     * @param {?Function} [Callback = null] The callback to execute after the Element has been fetched. Executes fetching from the server (if not already cached) synchronous if omitted.
     * @return {vDesk.Archive.Element|null} The cached Element; otherwise, null.
     */
    this.FetchElement = function(ID, Callback = null) {
        Ensure.Parameter(ID, Type.Number, "ID");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        const CachedElement = this.Find(ID);

        //Check if the Element already exists within the cache.
        if(CachedElement !== null) {
            //Check if a callback has been passed.
            if(Callback !== null) {
                Callback(CachedElement);
            } else {
                return CachedElement;
            }
        }
        //Otherwise fetch its data from the server.
        else {
            //Check if a callback has been passed.
            if(Callback !== null) {
                Callback(CachedElement);
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
                        //Check if the user can see the element.
                        if(Response.Status) {
                            const Element = vDesk.Archive.Element.FromDataView(Response.Data);
                            Elements.push(Element);
                            Callback(Element);
                        } else {
                            Callback(null);
                        }
                    });
            }
            //Otherwise fetch element synchronous.
            else {
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
                //Check if the user can see the element.
                if(Response.Status) {
                    const Element = vDesk.Archive.Element.FromDataView(Response.Data);
                    Elements.push(Element);
                    return Element;
                }
                return null;
            }
        }
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
     * Adds an Element to the cache.
     * @param {vDesk.Archive.Element} Element The element to add.
     * @throws TypeError
     */
    this.Add = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        Elements.push(Element);
    };

    /**
     * Fetches all child Elements of a specified Parent.
     * @param {Number} Parent The ID of the parent whose children should get fetched.
     * @param {Boolean} [Refetch=false] Determines, if cached elements should get fetched again.
     * @param {?Function} [Callback=null] If set, fetches elements asynchronously.
     * @return {Array<vDesk.Archive.Element>} An array containing the cached Elements.
     */
    this.FetchElements = function(Parent, Refetch = false, Callback = null) {
        Ensure.Parameter(Parent, Type.Number, "Parent");
        Ensure.Parameter(Refetch, Type.Boolean, "Refetch");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        //Checks if elements already have been fetched.
        let CachedElements = Elements.filter(Element => Element.Parent.ID === Parent);

        const Command = new vDesk.Modules.Command(
            {
                Module:     "Archive",
                Command:    "GetElements",
                Parameters: {ID: Parent},
                Ticket:     vDesk.User.Ticket
            }
        );

        //Check if a callback has been passed.
        if(Callback !== null) {
            //Check if childelements of the specified parent already have been fetched.
            if(CachedElements.length > 0) {
                //Check if the elements should be re-fetched.
                if(Refetch) {
                    vDesk.Connection.Send(
                        Command,
                        Response => {
                            if(Response.Status) {
                                //Filter Elements without an ID, discard any other.
                                CachedElements = CachedElements.filter(Element => Element.ID === null);

                                //Loop through resultset and replace and/or add elements.
                                Response.Data.forEach(DataView => {
                                    const Element = vDesk.Archive.Element.FromDataView(DataView);
                                    //Get index of the cached element if any.
                                    const Index = Elements.findIndex(CacheElement => CacheElement.ID !== null && CacheElement.ID === DataView.ID);
                                    //Check if the element already has been cached.
                                    if(~Index) {
                                        //If true, replace the cached one with the new one.
                                        Elements[Index] = Element;
                                    } else {
                                        //Else add it to the cache.
                                        Elements.push(Element);
                                    }
                                    CachedElements.push(Element);
                                });
                                Callback(CachedElements);
                            }
                        }
                    );
                } else {
                    Callback(CachedElements);
                }
            } else {
                //Else get elements from the server.
                vDesk.Connection.Send(
                    Command,
                    Response => {
                        if(Response.Status) {
                            //Loop through resultset.
                            Response.Data.forEach(DataView => {
                                //Create a new element and add it to the cache.
                                const Element = vDesk.Archive.Element.FromDataView(DataView);
                                Elements.push(Element);
                                CachedElements.push(Element);
                            });
                            //Check if a callback has been passed.
                            Callback(CachedElements);
                        }
                    }
                );
            }
        }
        //Otherwise fetch Elements synchronous.
        else {
            //Check if child Elements of the specified parent already have been fetched.
            if(CachedElements.length > 0) {
                //Check if the elements should be refetched
                if(Refetch) {
                    const Response = vDesk.Connection.Send(Command);
                    if(Response.Status) {
                        //Filter Elements without an ID, discard any other.
                        CachedElements = CachedElements.filter(Element => Element.ID === null);

                        //Loop through resultset and replace and/or add Elements.
                        Response.Data.forEach(DataView => {
                            const Element = vDesk.Archive.Element.FromDataView(DataView);
                            //Get index of the cached element if any.
                            const Index = Elements.findIndex(CacheElement => CacheElement.ID !== null && CacheElement.ID === DataView.ID);
                            //Check if the Element already has been cached.
                            if(~Index) {
                                //If true, replace the cached one with the new one.
                                Elements[Index] = Element;
                            } else {
                                //Else add it to the cache.
                                Elements.push(Element);
                            }
                            CachedElements.push(Element);
                        });
                        return CachedElements;
                    }
                } else {
                    return CachedElements;
                }
            } else {
                //Else get Elements from the server.
                const Response = vDesk.Connection.Send(Command);
                if(Response.Status) {
                    //Loop through resultset.
                    Response.Data.forEach(DataView => {
                        //Create a new Element and add it to the cache.
                        const Element = vDesk.Archive.Element.FromDataView(DataView);
                        Elements.push(Element);
                        CachedElements.push(Element);
                    });
                    return CachedElements;
                }
            }
        }

    };

    /**
     * Removes an Element from the ElementCache.
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
     * Finds an Element within the ElementCache.
     * @param {Number} ID The ID of the Element to find.
     * @return {vDesk.Archive.Element|null} The found Element; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "ID");
        return Elements.find(Element => Element.ID === ID) || null;
    };

};
