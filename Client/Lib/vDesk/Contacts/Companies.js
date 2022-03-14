"use strict";
/**
 * @typedef {Object} Company Represents a company.
 * @property {Number} ID Gets the ID of the company.
 * @property {String} Name Gets the name of the company.
 */
/**
 * Collection of all companies.
 * @type Array<Company>
 * @property {Function} Load Fetches all companies from the server and fills the collection. Removes any previous fetched.
 * @memberOf vDesk.Contacts
 * @package vDesk\Contacts
 */
vDesk.Contacts.Companies = [];
vDesk.Load.Companies = {
    Status: "Loading companies",
    Load:   function() {
        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Contacts",
                    Command:    "GetCompanyViews",
                    Parameters: {},
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            )
        );
        if(Response.Status){
            vDesk.Contacts.Companies.splice(0, this.length);
            Response.Data.forEach(Company => vDesk.Contacts.Companies.push(Company));
        }
    }
};


