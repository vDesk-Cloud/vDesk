"use strict";
/**
 * Collection of all users of vDesk.
 * @type Array
 * @property {Function} Load Fetches all usernames and ids from the server and fills the collection. Removes any previous fetched.
 * @package vDesk\Security
 */
vDesk.Security.Users = [];
vDesk.Load.Users = {
    Status: "Loading users",
    Load() {
        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "GetUsers",
                    Parameters: {View: true},
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            )
        );
        if(Response.Status){
            vDesk.Security.Users = Response.Data.map(User => vDesk.Security.User.FromDataView(User));
        }
    }
};
