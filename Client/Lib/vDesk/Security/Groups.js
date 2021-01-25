"use strict";
/**
 * Collection of all groups of vDesk.
 * @type Array
 * @property {Function} Load Fetches all groupnames and ids from the server and fills the collection. Removes any previous fetched.
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
                    Ticket:     vDesk.User.Ticket
                }
            )
        );
        if(Response.Status) {
            vDesk.Security.Groups = Response.Data.map(Group => vDesk.Security.Group.FromDataView(Group));
        }
    }
};