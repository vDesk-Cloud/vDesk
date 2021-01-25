"use strict";
/**
 *
 * Initializes a new instance of the Log class.
 * @class Plugin for viewing the system log of vDesk.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the Log plugin.
 * @memberOf vDesk.Configuration.Remote.Plugins
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Configuration.Remote.Plugins.Log = function Log() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Configuration.Log
        }
    });

    /**
     * Eventhandler that listens on the 'click' event and  refreshes the content of the log-table.
     */
    const OnClickRefreshButton = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Configuration",
                    Command:    "GetLog",
                    Parameters: {},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            FillTable,
            true
        );
    };

    /**
     * Eventhandler that listens on the 'click' event and clears the log and the contents of the log-table.
     */
    const OnClickClearButton = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Configuration",
                    Command:    "ClearLog",
                    Parameters: {},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    Table.Rows.Clear();
                } else {
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * Fills the table with all entries of the Log plugin.
     * @param {ArrayBuffer} Buffer The raw data of the Log plugin.
     */
    const FillTable = function(Buffer) {

        const Bytes = new Uint8Array(Buffer);

        if(Bytes.byteLength === 0) {
            return;
        }

        const Decoder = new TextDecoder("utf-8");
        const Rows = [];

        //Skip UTF-8 BOM if exists.
        let Offset = (Bytes[0] === 239 && Bytes[1] === 187 && Bytes[2] === 191) ? 3 : 0;

        //Parse the logfile.
        while(Offset < Bytes.length) {

            //Fetch line until next linefeed.
            let Index = Bytes.indexOf(0x0A, Offset);
            //Check if the last line has been reached.
            if(!~Index) {
                Index = Bytes.length;
            }
            const Line = Decoder.decode(Bytes.slice(Offset, Index++));
            Offset = Index;

            //Parse line and fill a new table row.
            const Row = Table.CreateRow();
            Row.Type = Line.substring(1, Line.indexOf("]"));
            Row.Date = new Date(Line.substring(Line.indexOf("(") + 1, Line.indexOf(")")));
            Row.Module = Line.substring(Line.indexOf("{") + 1, Line.indexOf("}"));
            Row.Entry = Line.substring(Line.indexOf("}") + 2, Line.length);

            switch(Row.Type) {
                case "ERROR" :
                    Row.Control.classList.add("Error");
                    break;
                case "WARN" :
                    Row.Control.classList.add("Warn");
                    break;
                case "INFO" :
                    Row.Control.classList.add("Info");
                    break;
                case "DEBUG":
                    Row.Control.classList.add("Debug");
                    break;
            }
            Rows.push(Row);

        }
        Table.Rows = Rows;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Log";

    /**
     * The table Container of the Log plugin.
     * @type {HTMLDivElement}
     */
    const Container = document.createElement("div");
    Container.className = "Container";
    Control.appendChild(Container);

    /**
     * The Table of the Log plugin.
     * @type {vDesk.Controls.Table}
     */
    const Table = new vDesk.Controls.Table(
        [
            {
                Name:  "Type",
                Label: vDesk.Locale.vDesk.Type,
                Type:  Type.String
            },
            {
                Name:  "Date",
                Label: vDesk.Locale.vDesk.MaximizeDate,
                Type:  Date
            },
            {
                Name:  "Module",
                Label: vDesk.Locale.vDesk.MaximizeModule,
                Type:  Type.String
            },
            {
                Name:  "Entry",
                Label: vDesk.Locale.Configuration.Entry,
                Type:  Type.String
            }
        ]
    );
    Container.appendChild(Table.Control);

    /**
     * The button container of the Log plugin.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Control.appendChild(Controls);

    /**
     * The refresh button of the Log plugin.
     * @type {HTMLButtonElement}
     */
    const Refresh = document.createElement("button");
    Refresh.className = "Button Icon";
    Refresh.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    Refresh.textContent = vDesk.Locale.vDesk.MaximizeRefresh;
    Refresh.addEventListener("click", OnClickRefreshButton, false);
    Controls.appendChild(Refresh);

    /**
     * The clear button of the Log plugin.
     * @type {HTMLButtonElement}
     */
    const Clear = document.createElement("button");
    Clear.className = "Button Icon";
    Clear.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete}")`;
    Clear.textContent = vDesk.Locale.vDesk.MaximizeClear;
    Clear.disabled = !vDesk.User.Permissions.UpdateSettings;
    Clear.addEventListener("click", OnClickClearButton, false);
    Controls.appendChild(Clear);

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Configuration",
                Command:    "GetLog",
                Parameters: {},
                Ticket:     vDesk.User.Ticket
            }
        ),
        FillTable,
        true
    );
};