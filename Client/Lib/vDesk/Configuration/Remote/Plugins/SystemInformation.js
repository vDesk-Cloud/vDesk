"use strict";
/**
 * Initializes a new instance of the SystemInformation class.
 * @class Represents plugin for displaying general informations about the system.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the systeminformation-plugin.
 * @memberOf vDesk.Configuration.Remote.Plugins
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Configuration.Remote.Plugins.SystemInformation = function SystemInformation() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Table.Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale["Configuration"]["SystemInformations"]
        }
    });

    /**
     * The Table of the SystemInformation plugin.
     * @type {vDesk.Controls.Table}
     */
    const Table = new vDesk.Controls.Table(
        [
            {
                Name:  "Key",
                Label: "Info",
                Type:  Type.String,
                Comparator: vDesk.Controls.Table.Column.Sort.String
            },
            {
                Name:  "Value",
                Label: " ",
                Type:  Type.String
            }
        ]
    );

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Configuration",
                Command:    "GetSystemInfo",
                Parameters: {},
                Ticket:     vDesk.User.Ticket
            }
        ),
        Response => {
            if(Response.Status) {
                const InstallDate = Table.CreateRow();
                InstallDate["Key"] = vDesk.Locale["Configuration"]["InstallDate"];
                InstallDate["Value"] = Response.Data[0];

                const FileCount = Table.CreateRow();
                FileCount["Key"] = vDesk.Locale["Configuration"]["FileCount"];
                FileCount["Value"] = Response.Data[2];

                const FolderCount = Table.CreateRow();
                FolderCount["Key"] = vDesk.Locale["Configuration"]["FolderCount"];
                FolderCount["Value"] = Response.Data[3];

                const DiskUsage = Table.CreateRow();
                DiskUsage["Key"] = vDesk.Locale["Configuration"]["DiskUsage"];
                DiskUsage["Value"] = Math.round((Number.parseInt(Response.Data[4]) / 1000) / 1000) + "Mb.";

                Table.Rows.Add(InstallDate);
                Table.Rows.Add(FileCount);
                Table.Rows.Add(FolderCount);
                Table.Rows.Add(DiskUsage);

            } else {
                alert(Response.Data);
            }
        }
    );
};
