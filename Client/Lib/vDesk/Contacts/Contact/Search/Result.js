"use strict";
/**
 * Initializes a new instance of the ContactSearchResult class.
 * @class Represents a searchresult for Contacts of a previous executed search.
 * @param {SearchResult} Result The data of the found contact.
 * @implements {vDesk.Search.IResult}
 * @memberOf vDesk.Contacts.Contact.Search
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 */
vDesk.Contacts.Contact.Search.Result = function Result(Result) {

    /**
     * The Contact of the Result.
     * @type vDesk.Contacts.Contact
     */
    let Contact = null;

    Object.defineProperties(this, {
        Viewer: {
            enumerable: true,
            get:        () => {
                if(Contact === null){
                    Contact = vDesk.Contacts.Contact.FromDataView(
                        vDesk.Connection.Send(
                            new vDesk.Modules.Command(
                                {
                                    Module:     "Contacts",
                                    Command:    "GetContact",
                                    Parameters: {ID: Result.Data.ID},
                                    Ticket:     vDesk.Security.User.Current.Ticket
                                }
                            )
                        ).Data
                    );
                }
                return new vDesk.Contacts.Contact.Viewer(Contact).Control;
            }
        },
        Icon:   {
            enumerable: true,
            value:      vDesk.Visual.Icons.Contacts.Module
        },
        Name:   {
            enumerable: true,
            value:      Result.Data.Forename + " " + Result.Data.Surname
        },
        Type:   {
            enumerable: true,
            value:      Result.Type
        }
    });
};
vDesk.Contacts.Contact.Search.Result.Implements(vDesk.Search.IResult);
vDesk.Search.Results.Contact = vDesk.Contacts.Contact.Search.Result;