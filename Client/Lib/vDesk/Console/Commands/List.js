"use strict";
/**
 * Console command that lists all registered Console commands.
 * @const
 * @type function
 * @param {Modules.Console} Console Executes the command on the specified Console.
 */
vDesk.Console.Commands.List = async Console => Console.Write(`[${Object.keys(vDesk.Console.Commands).join(", ")}]`);