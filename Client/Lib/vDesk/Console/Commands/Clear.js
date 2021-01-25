"use strict";
/**
 * Console command that clears the current Console.
 * @const
 * @type function
 * @param {Modules.Console} Console Executes the command on the specified Console.
 */
vDesk.Console.Commands.Clear = vDesk.Console.Commands.cls = async Console => Console.Clear();