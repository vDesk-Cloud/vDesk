"use strict";
/**
 * Console command that calls a command on the server.
 * @const
 * @type function
 * @param {Modules.Console} Console Executes the command on the specified Console.
 * @param {Object} [Arguments={}] Executes the command with the specified arguments.
 */
vDesk.Console.Commands.Call = async function(Console, Arguments = {}) {
    const Module = Arguments?.M ?? Arguments.Module;
    const Command = Arguments?.C ?? Arguments.Command;
    delete Arguments?.M;
    delete Arguments?.Module;
    delete Arguments?.C;
    delete Arguments?.Command;
    vDesk.Connection.Execute(
        new vDesk.Modules.Command(
            {
                Module,
                Command,
                Parameters: Arguments,
                Ticket:     Console.User.Ticket
            }
        )
    ).then(Response => Console.Write(JSON.stringify(Response.Data)));
};