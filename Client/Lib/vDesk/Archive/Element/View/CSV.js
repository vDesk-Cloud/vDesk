"use strict";
/**
 * Initializes a new instance of the CSV class.
 * @class Plugin for displaying the contents of a text file containing comma separated values.
 * @param {vDesk.Archive.Element} Element The element to display the file of.
 * @property {HTMLTableElement} Control Gets the underlying dom node.
 * @memberOf vDesk.Archive.Element.View
 * @package vDesk\Archive
 */
vDesk.Archive.Element.View.CSV = function CSV(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperty(this, "Control", {
        get: () => Table.Control
    });

    /**
     * The Table of the Log plugin.
     * @type {vDesk.Controls.Table}
     */
    const Table = new vDesk.Controls.Table();

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Archive",
                Command:    "Download",
                Parameters: {ID: Element.ID},
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        ),
        Buffer => {
            const Bytes = new Uint8Array(Buffer);
            const Decoder = new TextDecoder("utf-8");

            //Skip UTF-8 BOM if exists.
            let Offset = (Bytes[0] === 239 && Bytes[1] === 187 && Bytes[2] === 191) ? 3 : 0;

            //Parse header
            let Index = Bytes.indexOf(0x0A, Offset);
            Table.Columns = Decoder
                .decode(Bytes.slice(Offset, Index++))
                .split(";")
                .map(Header => new vDesk.Controls.Table.Column(
                    Header,
                    Type.String,
                    Header,
                    vDesk.Controls.Table.Column.Sort.String
                ));
            Offset = Index;

            //Parse CSV file.
            while(Offset < Bytes.length){
                //Fetch line until next linefeed.
                Index = Bytes.indexOf(0x0A, Offset);
                //Check if the last line has been reached.
                if(!~Index){
                    Index = Bytes.length;
                }
                const Row = Table.CreateRow();
                Decoder
                    .decode(Bytes.slice(Offset, Index++))
                    .split(";")
                    .forEach((Value, Index) => Row.Cells[Index].Value = Value);
                Table.Rows.Add(Row);
                Offset = Index;
            }
        },
        true
    );
};

/**
 * The file extensions the plugin can handle.
 * @enum {String}
 */
vDesk.Archive.Element.View.CSV.Extensions = ["csv"];
