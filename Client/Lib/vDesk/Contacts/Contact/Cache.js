"use strict";
/**
 * @typedef {Object} ContactStorage Storageobject for storing companies within the Cache.
 * @property {Boolean} Fetched Gets or sets a value indicating whether Contacts with the according letter have been already fetched.
 * @property {Array<vDesk.Contacts.Contact>} Contacts Gets or sets the Contacts of the ContactStorage
 */
/**
 * Initializes a new instance of the Cache class.
 * @class Provides functionality for fetching Contacts from the server and caching them.
 * @property {Array<vDesk.Contacts.Contact>} Contacts Gets the currently cached Contacts of the Cache.
 * @memberOf vDesk.Contacts.Contact
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 */
vDesk.Contacts.Contact.Cache = function Cache() {

    /**
     * Placeholder for non alphabetical letters.
     * @type String
     */
    const Symbol = "@";

    /**
     * The alphabetical index on the cached contact of the Cache.
     * @type Array<ContactStorage>
     */
    const Alphabet = [];

    /**
     * The cached contacts of the Cache.
     * @type Array<vDesk.Contacts.Contact>
     */
    const Contacts = [];

    Object.defineProperty(this, "Contacts", {
        enumerable: true,
        get:        () => Contacts
    });

    /**
     * Predicate for creating a key for accessing the ContactStorage-objects.
     * Creates a key for accessing the correct ContactStorage.
     * @param {String} Char The char to create a key of.
     * @return {String} The correct key to acces a ContactStorage.
     */
    const Key = Char => (/^[^a-zA-Z]/.test(Char)) ? Symbol : (Char.charAt(0)).toUpperCase();

    //Create alphabetical items.
    for(let i = 65; i < 91; i++){
        Alphabet[String.fromCharCode(i)] = {
            Fetched:  false,
            Contacts: []
        };
    }
    Alphabet[Symbol] = {
        Fetched:  false,
        Contacts: []
    };

    /**
     * Fetches a Contact from the server.
     * @param {Number} ID The ID of the Contact to fetch.
     * @param {Function} [Callback=null] The callback to execute if the Contact has been fetched from the server.
     * @return {vDesk.Contacts.Contact|null} The fetched Contact; otherwise, null if Callback has been omitted.
     */
    this.FetchContact = function(ID, Callback = null) {
        Ensure.Parameter(ID, Type.Number, "ID");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        //Setup Command.
        const Command = new vDesk.Modules.Command(
            {
                Module:     "Contacts",
                Command:    "GetContact",
                Parameters: {ID: ID},
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        );
        //Check if a callback has been passed.
        if(Callback !== null){
            vDesk.Connection.Send(
                Command,
                Response => {
                    if(Response.Status){
                        Callback(vDesk.Contacts.Contact.FromDataView(Response.Data));
                    }else{
                        Callback(null);
                    }
                }
            );
        }
        //Otherwise return the fetched contact.
        else{
            const Response = vDesk.Connection.Send(Command);
            if(Response.Status){
                return vDesk.Contacts.Contact.FromDataView(Response.Data);
            }else{
                return null;
            }
        }
    };

    /**
     * Fetches all Contacts from the server whose surname starts with the specified letter.
     * @param {String} Char The first letter of the surname of the Contacts to fetch.
     * @param {Number} Amount The amount of Contacts to fetch.
     * @param {Number} Offset The offset to start from.
     * @param {Boolean} [Refetch=false] Flag indicating whether aleady cached Contacts should be fetched again from the server.
     * @param {Function} [Callback=null] The callback to execute if the Contacts have been fetched from the server.
     * @return {Array<vDesk.Contacts.Contact>} The fetched contacts if Callback has been omitted.
     */
    this.FetchContacts = function(Char, Amount, Offset, Refetch = false, Callback = null) {
        Ensure.Parameter(Char, Type.String, "Char");
        Ensure.Parameter(Amount, Type.Number, "Amount");
        Ensure.Parameter(Offset, Type.Number, "Offset");
        Ensure.Parameter(Refetch, Type.Boolean, "Refetch");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);
        //Get alphabetical index.
        const Storage = Alphabet[Key(Char)];

        //check if Contacts whose surname begin with the passed letter have been fetched before.
        if(Storage.Fetched && !Refetch){
            //Check if the callback has been omitted.
            if(Callback === null){
                return Storage.Contacts;
            }
            Callback(Storage.Contacts);
        }
        //Otherwise fetch contacts from the server.
        else{
            //Check if the contacts of the specified alphabetical index should be fetched again.
            if(Refetch){
                //Clear the alphabetical index and remove the cached contacts from the cache.
                Storage.Contacts.forEach(Contact => {
                    Contacts.splice(Contacts.indexOf(Contact), 1);
                });
                Storage.Contacts.splice(0, Storage.Contacts.length);
            }
            //Setup Command.
            const Command = new vDesk.Modules.Command(
                {
                    Module:     "Contacts",
                    Command:    "GetContacts",
                    Parameters: {
                        Char:   Char,
                        Amount: Amount,
                        Offset: Offset
                    },
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            );
            //Check if a Callback has been passed.
            if(Callback !== null){
                vDesk.Connection.Send(
                    Command,
                    Response => {
                        if(Response.Status){
                            Storage.Contacts = Response.Data.map(Contact => vDesk.Contacts.Contact.FromDataView(Contact));
                            Storage.Fetched = true;
                            Storage.Contacts.forEach(Contact => Contacts.push(Contact));
                            Callback(Storage.Contacts);
                        }else{
                            alert(Response.Data);
                        }
                    }
                );
            }
            //Otherwise fetch and return the Contacts.
            else{
                const Response = vDesk.Connection.Send(Command);
                if(Response.Status){
                    Storage.Contacts = Response.Data.map(Contact => vDesk.Contacts.Contact.FromDataView(Contact));
                    Storage.Fetched = true;
                    Storage.Contacts.forEach(Contact => Contacts.push(Contact));
                    return Storage.Contacts;
                }else{
                    alert(Response.Data);
                }
            }
        }
    };

    /**
     * Adds a Contact to he Cache.
     * @param {vDesk.Contacts.Contact} Contact The Contact to add.
     * @return {Boolean} True if the Contact has been successfully added; otherwise, false.
     */
    this.Add = function(Contact) {
        Ensure.Parameter(Contact, vDesk.Contacts.Contact, "Contact");

        //Check if the contact does not exist within the cache.
        if(Contacts.find(CachedContact => CachedContact.ID === Contact.ID) === undefined){

            //Append the contact to its (maybe) new according alphabetical index and the cache.
            const Storage = Alphabet[Key(Contact.Surname).toUpperCase()];
            if(Storage.Fetched){
                Storage.Contacts.push(Contact);
                Contacts.push(Contact);
            }
            return true;
        }
        return false;
    };

    /**
     * Updates a Contact of the Cache.
     * @param {vDesk.Contacts.Contact} Contact The Contact to update.
     * @return {Boolean} True if the Contact has been successfully updated; otherwise, false.
     */
    this.Update = function(Contact) {
        Ensure.Parameter(Contact, vDesk.Contacts.Contact, "Contact");

        //Check if the contact exists within the cache.
        const CachedContact = Contacts.find(CachedContact => CachedContact.ID === Contact.ID);
        if(CachedContact !== undefined){

            //Remove the contact temporarily from its according alphabetical index.
            for(const Char in Alphabet){
                if(Alphabet[Char].Contacts.some(Contact => Contact.ID === CachedContact.ID)){
                    Alphabet[Char].Contacts.splice(Alphabet[Char].Contacts.indexOf(CachedContact), 1);
                    break;
                }
            }

            //Reappend the contact to its (maybe) new according alphabetical index.
            const Storage = Alphabet[Key(Contact.Surname)];
            if(Storage.Fetched){
                Storage.Contacts.push(Contact);
            }
            return true;
        }

        return false;
    };

    /**
     * Removes a Contact from the Cache.
     * @param {vDesk.Contacts.Contact} Contact The Contact to remove.
     * @return {Boolean} True if the Contact has been successfully removed; otherwise, false.
     */
    this.Remove = function(Contact) {
        Ensure.Parameter(Contact, vDesk.Contacts.Contact, "Contact");

        //Check if the contact exists within the cache.
        const CachedContact = Contacts.find(CachedContact => CachedContact.ID === Contact.ID);
        if(CachedContact !== undefined){

            //Remove the contact from its according alphabetical index.
            for(const Char in Alphabet){
                if(Alphabet[Char].Contacts.some(Contact => Contact.ID === CachedContact.ID)){
                    Alphabet[Char].Contacts.splice(Alphabet[Char].Contacts.indexOf(CachedContact), 1);
                    break;
                }
            }

            //Remove the contact from the cache.
            Contacts.splice(Contacts.indexOf(CachedContact), 1);
            return true;
        }
        return false;
    };

    /**
     * Searches the Cache for a Contact with a specified ID.
     * @param {Number} ID The ID of the Contact to find.
     * @return {vDesk.Contacts.Contact|null} The found Contact; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "ID");
        return Contacts.find(Contact => Contact.ID === ID) ?? null;
    };
};