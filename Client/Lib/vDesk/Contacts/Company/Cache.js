"use strict";
/**
 * @typedef {Object} CompanyStorage Storageobject for storing Companies within the Cache.
 * @property {Boolean} Fetched Gets or sets a value indicating whether Companys with the according letter have been already fetched.
 * @property {Array<vDesk.Contacts.Company>} Companies Gets or sets the contacs of the Companystorage
 */
/**
 * Initializes a new instance of the CompanyCache class.
 * @class Provides functionality for fetching Companies from the server and caching them.
 * @property {Array<vDesk.Contacts.Company>} Companies Gets the currently cached Companies of the Cache.
 * @memberOf vDesk.Contacts.Company
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 */
vDesk.Contacts.Company.Cache = function Cache() {

    /**
     * Placeholder for non alphabetical letters.
     * @type String
     */
    const Symbol = "@";

    /**
     * The alphabetical index on the cached Companies of the Cache.
     * @type Array<CompanyStorage>
     */
    const Alphabet = [];

    /**
     * The cached Companies of the Cache.
     * @type Array<vDesk.Contacts.Company>
     */
    const Companies = [];

    /**
     * Predicate for creating a key for accessing the Companystorage-objects.
     * @type Function
     */
    const Key = Char => (/^[^a-zA-Z]/.test(Char)) ? Symbol : (Char.charAt(0)).toUpperCase();

    Object.defineProperty(this, "Companies", {
        enumerable: true,
        get:        () => Companies
    });

    //Create alphabetical items.
    for(let i = 65; i < 91; i++){
        Alphabet[String.fromCharCode(i)] = {
            Fetched:   false,
            Companies: []
        };
    }
    Alphabet[Symbol] = {
        Fetched:   false,
        Companies: []
    };

    /**
     * Fetches a Company from the server.
     * @param {Number} ID The ID of the Company to fetch.
     * @param {Function} [Callback=null] The callback to execute if the Company has been fetched from the server.
     * @return {vDesk.Contacts.Company|null} The fetched Company or null if Callback has been omitted.
     */
    this.FetchCompany = function(ID, Callback = null) {
        Ensure.Parameter(ID, Type.Number, "ID");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        //Setup Command.
        const Command = new vDesk.Modules.Command(
            {
                Module:     "Contacts",
                Command:    "GetCompany",
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
                        Callback(vDesk.Contacts.Company.FromDataView(Response.Data));
                    }else{
                        Callback(null);
                    }
                }
            );
        }
        //Otherwise return the fetched Company.
        else{
            const Response = vDesk.Connection.Send(Command);
            if(Response.Status){
                return vDesk.Contacts.Company.FromDataView(Response.Data);
            }else{
                return null;
            }
        }
    };

    /**
     * Fetches all Companies from the server whose name starts with the specified letter.
     * @param {String} Char The first letter of the name of the Companies to fetch.
     * @param {Number} Amount The amount of Companies to fetch.
     * @param {Number} Offset The offset to start from.
     * @param {?Boolean} [Refetch = false] Flag indicating whether already cached Companies should be fetched again from the server.
     * @param {Function} [Callback] The callback to execute if the Companies have been fetched from the server.
     * @return {Array<vDesk.Contacts.Company>} The fetched Companies if Callback has been omitted.
     */
    this.FetchCompanies = function(Char, Amount, Offset, Refetch = false, Callback = null) {
        Ensure.Parameter(Char, Type.String, "Char");
        Ensure.Parameter(Amount, Type.Number, "Amount");
        Ensure.Parameter(Offset, Type.Number, "Offset");
        Ensure.Parameter(Refetch, Type.Boolean, "Refetch");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        //Get alphabetical index.
        const Storage = Alphabet[Key(Char)];

        //check if Companies whose name begin with the passed letter have been fetched before.
        if(Storage.Fetched && !Refetch){
            //Check if the callback has been omitted.
            if(Callback === null){
                return Storage.Companies;
            }
            Callback(Storage.Companies);
        }
        //Otherwise fetch Companies from the server.
        else{
            //Check if the Companies of the specified alphabetical index should be fetched again.
            if(Refetch){
                //Clear the alphabetical index and remove the cached Companies from the cache.
                Storage.Companies.forEach(Company => {
                    Companies.splice(Companies.indexOf(Company), 1);
                });
                Storage.Companies.splice(0, Storage.Companies.length);
            }
            //Setup Command.
            const Command = new vDesk.Modules.Command(
                {
                    Module:     "Contacts",
                    Command:    "GetCompanies",
                    Parameters: {
                        Char:   Char,
                        Amount: Amount,
                        Offset: Offset
                    },
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            );
            //Check if a callback has been passed.
            if(Callback !== null){
                vDesk.Connection.Send(
                    Command,
                    Response => {
                        if(Response.Status){
                            Storage.Companies = Response.Data.map(Company => vDesk.Contacts.Company.FromDataView(Company));
                            Storage.Fetched = true;
                            Storage.Companies.forEach(Company => Companies.push(Company));
                            Callback(Storage.Companies);
                        }else{
                            alert(Response.Data);
                        }
                    }
                );
            }
            //Otherwise fetch and return the Companies.
            else{
                const Response = vDesk.Connection.Send(Command);
                if(Response.Status){
                    Storage.Companies = Response.Data.map(Company => vDesk.Contacts.Company.FromDataView(Company));
                    Storage.Fetched = true;
                    Storage.Companies.forEach(Company => Companies.push(Company));
                    return Storage.Companies;
                }else{
                    alert(Response.Data);
                }
            }
        }
    };

    /**
     * Adds a Company to he CompanyCache.
     * @param {vDesk.Contacts.Company} Company The Company to add.
     * @return {Boolean} True if the Company has been successfully added; otherwise, false.
     */
    this.Add = function(Company) {
        Ensure.Parameter(Company, vDesk.Contacts.Company, "Company");

        //Check if the Company does not exist within the cache.
        if(Companies.find(CachedCompany => CachedCompany.ID === Company.ID) === undefined){

            //Append the Company to its (maybe) new according alphabetical index and the cache.
            const Storage = Alphabet[Key(Company.Name)];
            if(Storage.Fetched){
                Storage.Companies.push(Company);
                Companies.push(Company);
            }
            return true;
        }
        return false;
    };

    /**
     * Updates a Company of the CompanyCache.
     * @param {vDesk.Contacts.Company} Company The Company to update.
     * @return {Boolean} True if the Company has been successfully updated; otherwise, false.
     */
    this.Update = function(Company) {
        Ensure.Parameter(Company, vDesk.Contacts.Company, "Company");

        //Check if the Company exists within the cache.
        const CachedCompany = Companies.find(CachedCompany => CachedCompany.ID === Company.ID);
        if(CachedCompany !== undefined){

            //Remove the Company temporarily from its according alphabetical index.
            for(const Char in Alphabet){
                if(Alphabet[Char].Companies.some(Company => Company.ID === CachedCompany.ID)){
                    Alphabet[Char].Companies.splice(Alphabet[Char].Companies.indexOf(CachedCompany), 1);
                    break;
                }
            }

            //Reappend the Company to its (maybe) new according alphabetical index.
            const Storage = Alphabet[Key(Company.Name)];
            if(Storage.Fetched){
                Storage.Companies.push(Company);
            }
            return true;
        }

        return false;
    };

    /**
     * Removes a Company from the CompanyCache.
     * @param {vDesk.Contacts.Company} Company The Company to remove.
     * @return {Boolean} True if the Company has been successfully removed; otherwise, false.
     */
    this.Remove = function(Company) {
        Ensure.Parameter(Company, vDesk.Contacts.Company, "Company");

        //Check if the Company exists within the cache.
        const CachedCompany = Companies.find(CachedCompany => CachedCompany.ID === Company.ID);
        if(CachedCompany !== undefined){

            //Remove the Company from its according alphabetical index.
            for(const Char in Alphabet){
                if(Alphabet[Char].Companies.some(Company => Company.ID === CachedCompany.ID)){
                    Alphabet[Char].Companies.splice(Alphabet[Char].Companies.indexOf(CachedCompany), 1);
                    break;
                }
            }

            //Remove the Company from the cache.
            Companies.splice(Companies.indexOf(CachedCompany), 1);
            return true;
        }
        return false;
    };

    /**
     * Searches the CompanyCache for a Company Company a specified ID.
     * @param {Number} ID The ID of the Company to find.
     * @return {vDesk.Contacts.Company|null} The Company if found; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "ID");
        return Companies.find(Company => Company.ID === ID) ?? null;
    };
};