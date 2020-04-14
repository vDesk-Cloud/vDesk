"use strict";
/**
 * Namespace that contains Messenger related Classes.
 * @namespace Messenger
 * @memberOf vDesk
 */
vDesk.Messenger = {
    Status: "Initializing Messenger",
    /**
     * Loads the Messenger Module..
     * @name vDesk.Messenger.Load
     * @type {Function}
     */
    Load:   () => vDesk.Header.Menu.Add(
        new vDesk.Menu.Item(
            "Messages",
            vDesk.Visual.Icons.Messenger.Message,
            () => vDesk.Modules["Messenger"].Show()
        )
    )
};

vDesk.Load.Messenger = vDesk.Messenger;