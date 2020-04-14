"use strict";
/**
 * Initializes a new instance of the Settings class.
 * Provides access to local and remote accessible configurationvalues.
 * @namespace
 * @memberOf vDesk.Configuration
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Configuration.Settings = {

    /**
     * Contains 'temporary' setting-values.
     * @type {Array|Object}
     */
    Temp: JSON.parse(sessionStorage.getItem("Temp") || "{}"),

    /**
     * The local settings of the Settings?
     * @type {Object<String>}
     */
    Local: {},

    /**
     * Contains server-side setting-values.
     * @type {Object<String>}
     */
    Remote: {},

    /**
     * Saves the values of the configuration settings.
     */
    Save() {
        localStorage.setItem(vDesk.User.Name, JSON.stringify(this.Local));
        sessionStorage.setItem("Temp", JSON.stringify(this.Temp));
    }

};
vDesk.Load.Configuration = {
    Status: "Loading settings",
    Load() {

        vDesk.Configuration.Settings.Local = new Proxy(
            JSON.parse(localStorage.getItem(vDesk.User.Name) || "{}"),
            {
                get: (LocalSettings, Domain) => {
                    if(!(Domain in LocalSettings)) {
                        LocalSettings[Domain] = {};
                    }
                    return LocalSettings[Domain];
                },
                set: (LocalSettings, Domain, Value) => LocalSettings[Domain] = Value
            }
        );

        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:  "Configuration",
                    Command: "GetSettings",
                    Ticket:  vDesk.User.Ticket
                }
            )
        );
        if(Response.Status) {
            vDesk.Configuration.Settings.Remote = Response.Data;
            vDesk.Configuration.Settings.remote = vDesk.Configuration.Settings.Remote;
        }
    }
};
vDesk.Unload.Configuration = {
    Status: "Saving settings",
    Unload() {
        vDesk.Configuration.Settings.Save();
        vDesk.Configuration.Settings.Local = {};
        vDesk.Configuration.Settings.Remote = {};
    }
};