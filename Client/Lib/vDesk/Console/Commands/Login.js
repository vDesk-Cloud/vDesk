"use strict";
/**
 * Console command that changes the current user of the specified Console.
 * @const
 * @type function
 * @param {Modules.Console} Console Executes the command on the specified Console.
 * @param {Object} [Arguments={}] Executes the command with the specified arguments.
 */
vDesk.Console.Commands.Login = async function(Console, Arguments = {}) {
    const Response = await vDesk.Connection.Execute(
        new vDesk.Modules.Command(
            {
                Module:     "Security",
                Command:    "Login",
                Parameters: {
                    User:     Arguments.User,
                    Password: Arguments.Password
                }
            }
        )
    );
    if(Response.Status) {
        Console.User = vDesk.Security.User.FromDataView(Response.Data);
        Console.Write(`Changed context to User '${Console.User.Name}'`);
    }
};