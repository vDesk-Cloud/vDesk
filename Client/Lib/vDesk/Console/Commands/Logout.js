"use strict";
/**
 * Console command that changes the current user of the specified Console.
 * @const
 * @type function
 * @param {Modules.Console} Console Executes the command on the specified Console.
 * @param {Object} [Arguments={}] Executes the command with the specified arguments.
 */
vDesk.Console.Commands.Logout = async function(Console, Arguments = {}) {
    if(Console.User !== vDesk.User) {
        const Response = await vDesk.Connection.Execute(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "Logout",
                    Parameters: {User: Console.User.Name},
                    Ticket:     Console.User.Ticket
                }
            )
        );
        if(Response.Status) {
            Console.User = vDesk.User;
            Console.Write(`Changed context to User '${Console.User.Name}'`);
        }
    }
};