/**
 * Collection of existing Masks.
 * @type Array<vDesk.MetaInformation.Mask>
 */
vDesk.MetaInformation.Masks = [];
vDesk.Load.Masks = {
    Status: "Loading meta information",
    Load:   function() {
        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "MetaInformation",
                    Command:    "GetMasks",
                    Parameters: {},
                    Ticket:     vDesk.User.Ticket
                }
            )
        );
        if(Response.Status) {
            vDesk.MetaInformation.Masks.splice(0, this.length);
            Response.Data.forEach(Mask => vDesk.MetaInformation.Masks.push(vDesk.MetaInformation.Mask.FromDataView(Mask)));
        }
    }
};