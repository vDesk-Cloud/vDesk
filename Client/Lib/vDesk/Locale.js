"use strict";
/**
 * @typedef {Object} Country
 * @property {String} Code The code of the Country.
 * @property {String} Name The name of the Country.
 */

/**
 * Proxy that represents an empty translation domain.
 * @type {{}}
 */
const EmptyDomain = new Proxy({}, {get: () => "[Undefined Translation]"});

/**
 * @namespace Locale
 * @memberOf vDesk
 * @type {{Status: string, Load: vDesk.Locale.Load, Translations: {}}}
 * @package vDesk\Locale
 */
vDesk.Locale = new Proxy(
    {
        /**
         * @type {Array<String>}
         * @name vDesk.Locale.Locales
         */
        Locales: [],

        /**
         * @type {Object<Proxy>}
         * @name vDesk.Locale.Translations
         */
        Translations: {},
        /**
         * @type {Array<vDesk.Locale.Country>}
         * @name vDesk.Locale.Countries
         */
        Countries: [],

        /**
         * Initializes a new instance of the Country class.
         * @param {?String} [Code=null] Initializes the Country with the specified code.
         * @param {String} [Name=""] Initializes the Country with the specified name.
         * @property {?String} Code Gets or sets the code of the Country.
         * @property {String} Name Gets or sets the name of the Country.
         * @memberOf vDesk.Locale
         * @author Kerry <DevelopmentHero@gmail.com>
         * @version 1.0.0.
         */
        Country: function(Code = null, Name = "") {
            Ensure.Parameter(Code, Type.String, "Code", true);
            Ensure.Parameter(Name, Type.String, "Name");
            Object.defineProperties(this, {
                Code: {
                    enumerable: true,
                    get:        () => Code,
                    set:        Value => {
                        Ensure.Property(Value, Type.String, "Code", true);
                        Code = Value;
                    }
                },
                Name: {
                    enumerable: true,
                    get:        () => Name,
                    set:        Value => {
                        Ensure.Property(Value, Type.String, "Name");
                        Name = Value;
                    }
                }
            });
        },
        /**
         * Loads the translations of a specified locale.
         * @param {String} Locale The locale to load.
         * @name vDesk.Locale.Load
         */
        Load:   function(Locale = vDesk.Security.User.Current.Locale) {
            //Load locales.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:  "Locale",
                        Command: "GetLocales",
                        Ticket:  vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        this.Locales = Response.Data;
                    }
                }
            );

            //@todo Check if fetching only a domain instead of an entire locale is better for performance?
            //Load translations.
            const Response = vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Locale",
                        Command:    "GetLocale",
                        Parameters: {Locale: Locale},
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                )
            );
            if(Response.Status){
                //Apply proxies.
                for(const Domain in Response.Data){
                    new Proxy(Response.Data[Domain], {get: (Domain, Tag) => Domain?.[Tag] ?? "[Undefined Translation]"});
                }
                this.Translations = new Proxy(Response.Data, {get: (Translations, Domain) => Translations?.[Domain] ?? EmptyDomain});
            }

            //Load countries.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Locale",
                        Command:    "GetCountries",
                        Parameters: {Locale: Locale},
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        this.Countries = Response.Data.map(Country => vDesk.Locale.Country.FromDataView(Country));
                    }
                }
            );
        },
        Status: "Loading translations"
    },
    {get: (Locale, Property) => Locale?.[Property] ?? Locale.Translations[Property]}
);

/**
 * Factory method that creates a Country from a JSON-encoded representation.
 * @param {Object} DataView The data to use to create an instance of the Country.
 * @return {vDesk.Locale.Country} A Country filled with the provided data.
 */
vDesk.Locale.Country.FromDataView = function(DataView) {
    return new vDesk.Locale.Country(
        DataView?.Code ?? null,
        DataView?.Name ?? ""
    );
};

vDesk.Load.Locale = vDesk.Locale;