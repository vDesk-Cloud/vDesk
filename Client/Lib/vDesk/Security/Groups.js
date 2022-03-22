"use strict";
/**
 * Collection of all groups of vDesk.
 * @type Array
 * @property {Function} Load Fetches all groupnames and ids from the server and fills the collection. Removes any previous fetched.
 * @package vDesk\Security
 */
vDesk.Security.Groups = [];
vDesk.Load.Groups = {
    Status: "Loading groups",
    Load() {
        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "GetGroups",
                    Parameters: {View: true},
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            )
        );
        if(Response.Status){
            vDesk.Security.Groups = Response.Data.map(Group => vDesk.Security.Group.FromDataView(Group));
        }
    }
};