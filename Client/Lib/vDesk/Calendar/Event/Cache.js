"use strict";
/**
 * @typedef {Object} EventStorage
 * @property {Date} Date The date representation of the month.
 * @property {Array<vDesk.Calendar.Event>} Events The Events which occur within the month.
 */
/**
 * Initializes a new instance of the Cache class.
 * @class Provides functionality for fetching Events from the server and caching them.
 * @param {Number} [Size=vDesk.Calendar.Event.Cache.DefaultCacheSize] Initializes the Cache with the specified amount of months to cache.
 * @param {Boolean} [Prefetch=false] Flag indicating whether the to prefetch the specified amount of months.
 * @property {Array<vDesk.Calendar.Event>} Events Gets the cached Events of the Cache.
 * @property {Array<EventStorage>} Months Gets the cached months and their Events of the Cache.
 * @memberOf vDesk.Calendar.Event
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Calendar
 */
vDesk.Calendar.Event.Cache = function Cache(Size = vDesk.Calendar.Event.Cache.DefaultCacheSize, Prefetch = false) {
    Ensure.Parameter(Size, Type.Number, "Size");
    Ensure.Parameter(Prefetch, Type.Boolean, "Prefetch");

    /**
     * The cached Events of the Cache.
     * @type {Array<vDesk.Calendar.Event>}
     */
    const Cache = [];

    /**
     * The cached months of the Cache.
     * @type {Array<EventStorage>}
     */
    const Months = [];

    Object.defineProperties(this, {
        Events: {
            enumerable: true,
            get:        () => Cache
        },
        Months: {
            enumerable: true,
            get:        () => Months
        },
        Size:   {
            enumerable: true,
            get:        () => Size,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Size");
                Size = Value;
            }
        }
    });

    /**
     * Returns the current status of the Cache.
     * @return {String} The formatted status of the Cache.
     */
    this.Status = () => `Cached months: ${Months.length}, Events: ${Cache.length}`;

    /**
     * Fetches an Event.
     * @param {Number} ID The ID of the Event to fetch.
     * @param {Function} [Callback=null] The callback to execute if the Event has been fetched from the server.
     * @return {vDesk.Calendar.Event|null} The fetched Event; otherwise, null if Callback has been omitted.
     */
    this.FetchEvent = function(ID, Callback = null) {
        Ensure.Parameter(ID, Type.Number, "ID");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        //Setup Command.
        const Command = new vDesk.Modules.Command(
            {
                Module:     "Calendar",
                Command:    "GetEvent",
                Parameters: {ID: ID},
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        );
        //Check if a callback has been passed.
        if(Callback !== null){
            vDesk.Connection.Send(
                Command,
                Response => {
                    if(Response.Status){
                        Callback(vDesk.Calendar.Event.FromDataView(Response.Data));
                    }else{
                        Callback(null);
                    }
                }
            );
        }
        //Otherwise return the fetched Event.
        else{
            const Response = vDesk.Connection.Send(Command);
            if(Response.Status){
                return vDesk.Calendar.Event.FromDataView(Response.Data);
            }else{
                return null;
            }
        }
    };

    /**
     * Gets all Events occurring at a specified day in a specified month.
     * @param {Date} Day The day to fetch.
     * @param {Boolean} [Refetch=false] Determines whether cached Events should get fetched again.
     * @return {Array<vDesk.Calendar.Event>} The Events occurring at the specified day.
     */
    this.GetDay = function(Day, Refetch = false) {
        Ensure.Parameter(Day, Date, "Day");
        Ensure.Parameter(Refetch, Type.Boolean, "Refetch");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);
        return this.GetMonth(Day, Refetch)
            .filter(
                Event => (Event.Start < Day && Event.End > Day)
                    || (Event.Start.getDate() === Day.getDate())
                    || (Event.End.getDate() === Day.getDate() && (Event.End.getMinutes() > 0 || Event.End.getHours() > 0))
            );
    };

    /**
     * Fetches all Events at a specified day in a specified month.
     * @param {Date} Day The day to fetch.
     * @param {Function} Callback The callback to execute when the Events have been fetched from the server..
     * @param {Boolean} [Refetch=false] Determines whether cached Events should get fetched again.
     */
    this.FetchDay = function(Day, Callback, Refetch = false) {
        Ensure.Parameter(Day, Date, "Day");
        Ensure.Parameter(Refetch, Type.Boolean, "Refetch");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);
        this.FetchMonth(
            Day,
            Month => Callback(
                Month.filter(
                    Event => Event.Start < Day && Event.End > Day
                        || Event.Start.getDate() === Day.getDate()
                        || Event.End.getDate() === Day.getDate()
                        && (Event.End.getMinutes() > 0 || Event.End.getHours() > 0)
                )
            ),
            Refetch
        );
    };

    /**
     * Adds an array of Events to the Cache, discarding duplicate Events.
     * @param {Array<vDesk.Calendar.Event>} Events The Events to add.
     * @param {Boolean} [Replace=false] Flag indicating whether any duplicate Events should be replaced.
     * @return {Array<vDesk.Calendar.Event>} Unique array of the added Events.
     */
    this.AddEvents = function(Events, Replace = false) {
        Ensure.Parameter(Events, Array, "Events");
        Ensure.Parameter(Replace, Type.Boolean, "Replace");

        const UniqueEvents = [];
        Events.forEach(Event => {

            //Check if the Event exists already within the Cache.
            const CachedEvent = Cache.find(CachedEvent => CachedEvent.ID === Event.ID);
            if(CachedEvent !== undefined){

                //Check if the cached Event should get replaced with the fetched Event.
                if(Replace){
                    Cache[Events.indexOf(CachedEvent)] = Event;
                    UniqueEvents.push(Event);
                }else{
                    UniqueEvents.push(CachedEvent);
                }
            }
            //Otherwise add the Event to the Cache.
            else{
                Cache.push(Event);
                UniqueEvents.push(Event);
            }
        });
        return UniqueEvents;
    };

    /**
     * Gets all Events within a specified month.
     * @param {Date} Month The month to fetch.
     * @param {Boolean} [Refetch=false] Determines, if cached Events should get fetched again.
     * @return {Array<vDesk.Calendar.Event>} An array containing the Events retrieved from the server.
     */
    this.GetMonth = function(Month, Refetch = false) {
        Ensure.Parameter(Month, Date, "Month");
        Ensure.Parameter(Refetch, Type.Boolean, "Refetch");

        const From = Month.clone();
        From.setDate(1);
        const To = From.clone();
        To.setMonth(To.getMonth() + 1);
        To.setDate(0);

        const CachedMonth = Months.find(
            CachedMonth => CachedMonth.Date.getFullYear() === Month.getFullYear()
                && CachedMonth.Date.getMonth() === Month.getMonth()
        );

        if(CachedMonth === undefined){
            if(Months.length === Size){
                Months.shift().Events.forEach(Event => Cache.splice(Cache.indexOf(Event), 1));
            }
            const Response = vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Calendar",
                        Command:    "GetEvents",
                        Parameters: {
                            From,
                            To
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                )
            );
            if(Response.Status){
                Months.push(
                    {
                        Date:   From,
                        Events: this.AddEvents(Response.Data.map(Event => vDesk.Calendar.Event.FromDataView(Event)))
                    }
                );
                return Months[Months.length - 1].Events;
            }
        }
        if(Refetch){
            const Response = vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Calendar",
                        Command:    "GetEvents",
                        Parameters: {
                            From,
                            To
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                )
            );
            if(Response.Status){
                Months[Months.indexOf(CachedMonth)] = {
                    Date:   From,
                    Events: this.AddEvents(Response.Data.map(Event => new vDesk.Calendar.Event(Event)), true)
                };
                return Months[Months.length - 1].Events;
            }
        }else{
            return CachedMonth.Events;
        }
        return [];
    };

    /**
     * Fetches all Events within a specified month asynchronously from the server.
     * @param {Date} Month The month to fetch.
     * @param {Function} Callback The callback to execute when the Events have been fetched from the server.
     * @param {Boolean} [Refetch=false] Determines, if cached Events should get fetched again.
     */
    this.FetchMonth = function(Month, Callback, Refetch = false) {
        Ensure.Parameter(Month, Date, "Month");
        Ensure.Parameter(Callback, Type.Function, "Callback");
        Ensure.Parameter(Refetch, Type.Boolean, "Refetch");

        const From = Month.clone();
        From.setDate(1);
        const To = From.clone();
        To.setMonth(To.getMonth() + 1);
        To.setDate(0);

        const CachedMonth = Months.find(
            CachedMonth => CachedMonth.Date.getFullYear() === Month.getFullYear()
                && CachedMonth.Date.getMonth() === Month.getMonth()
        );

        if(CachedMonth === undefined){
            if(Months.length === Size){
                Months.shift().Events.forEach(Event => Cache.splice(Cache.indexOf(Event), 1));
            }
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Calendar",
                        Command:    "GetEvents",
                        Parameters: {
                            From,
                            To
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        Months.push(
                            {
                                Date:   From,
                                Events: this.AddEvents(Response.Data.map(Event => vDesk.Calendar.Event.FromDataView(Event)))
                            }
                        );
                        Callback(Months[Months.length - 1].Events);
                    }
                });

        }else{
            if(Refetch){
                vDesk.Connection.Send(
                    new vDesk.Modules.Command(
                        {
                            Module:     "Calendar",
                            Command:    "GetEvents",
                            Parameters: {
                                From,
                                To
                            },
                            Ticket:     vDesk.Security.User.Current.Ticket
                        }
                    ),
                    Response => {
                        if(Response.Status){
                            Months.push(
                                {
                                    Date:   From,
                                    Events: this.AddEvents(Response.Data.map(Event => vDesk.Calendar.Event.FromDataView(Event)), true)
                                }
                            );
                            Callback(Months[Months.length - 1].Events);
                        }
                    });
            }else{
                Callback(CachedMonth.Events);
            }
        }
        return [];
    };

    //Prefetch months since actual month.
    if(Prefetch){
        const Today = new Date();
        for(let i = 0; i < Size; i++){
            this.FetchMonth(Today, () => {
            });
            Today.setMonth(Today.getMonth() + 1);
        }
    }

    /**
     * Adds an Event to the Cache.
     * @param {vDesk.Calendar.Event} Event The Event to add.
     * @param {Boolean} [Replace=false] Flag indicating whether any cached duplicate Event will be replaced with the specified Event.
     * @return {Boolean} True if the Event has been successfully added; otherwise, false.
     */
    this.Add = function(Event, Replace = false) {
        Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");
        if(Cache.find(CachedEvent => CachedEvent.ID === Event.ID) === undefined){
            Cache.push(Event);
            //Append Event on matching months.
            Months.filter(Month => {
                const LastDate = Month.Date.clone();
                LastDate.setMonth(LastDate.getMonth() + 1);
                LastDate.setDate(0);
                return ((Event.Start > Month.Date) && (Event.End < LastDate))
                    || ((Event.Start > Month.Date && Event.Start < LastDate) && (Event.End > LastDate))
                    || ((Event.Start < Month.Date) && (Event.End > Month.Date && Event.End < LastDate))
                    || ((Event.Start < Month.Date) && (Event.End > LastDate));

            }).forEach(Month => Month.Events.push(Event));

            return true;
        }
        return false;
    };

    /**
     * Updates an Event of the Cache.
     * @param {vDesk.Calendar.Event} Event The Event to update.
     * @return {Boolean} True if the Event has been successfully updated; otherwise, false.
     */
    this.Update = function(Event) {
        Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");
        //Check if the Event exists within the Cache.
        const CachedEvent = Cache.find(CachedEvent => CachedEvent.ID === Event.ID);
        if(CachedEvent !== undefined){
            Cache[Cache.indexOf(CachedEvent)] = Event;
            //Replace Event in months.
            Months.forEach(Month => {
                const CachedEvent = Month.Events.find(CachedEvent => CachedEvent.ID === Event.ID);
                if(CachedEvent !== undefined){
                    Month.Events[Month.Events.indexOf(CachedEvent)] = Event;
                }
            });
            return true;
        }
        return false;
    };

    /**
     * Removes an Event from the Cache.
     * @param {vDesk.Calendar.Event} Event The Event to remove.
     * @return {Boolean} True if the Event has been successfully removed; otherwise, false.
     */
    this.Remove = function(Event) {
        Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");

        const CachedEvent = Cache.find(CachedEvent => CachedEvent.ID === Event.ID);
        //Remove the Event from the cache if it exists.
        if(CachedEvent !== undefined){
            Cache.splice(Cache.indexOf(CachedEvent), 1);

            //Remove Event in months.
            Months.forEach(Month => {
                const CachedEvent = Month.Events.find(CachedEvent => CachedEvent.ID === Event.ID);
                if(CachedEvent !== undefined){
                    Month.Events.splice(Month.Events.indexOf(CachedEvent), 1);
                }
            });
            return true;
        }
        return false;
    };

    /**
     * Searches the Cache for an Event with a specified ID.
     * @param {Number} ID The ID of the Event to find.
     * @return {vDesk.Calendar.Event|null} The found Event; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "ID");
        return Cache.find(Event => Event.ID === ID) ?? null;
    };
};

/**
 * Defines the default amount of months, the Eventcache will prehold.
 * @constant
 * @type {Number}
 * @name vDesk.Calendar.Event.Cache.DefaultCacheSize
 */
vDesk.Calendar.Event.Cache.DefaultCacheSize = 5;