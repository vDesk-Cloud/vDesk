"use strict";
/**
 * Initializes a new instance of the Mask class.
 * @class Represents the structure of a Mask.
 * @param {?Number} [ID=null] The ID of the Mask.
 * @param [Name=vDesk.Locale.MetaInformation.NewMask] The name of the Mask.
 * @param {Array<vDesk.MetaInformation.Mask.Row>} [Rows=[]] The Rows of the Mask.
 * @property {Number} ID Gets or sets the ID of the Mask.
 * @property {String} Name Gets or sets the name of the Mask.
 * @property {Array<vDesk.MetaInformation.Mask.Row>} Rows Gets or sets the Rows of the Mask.
 * @memberOf vDesk.MetaInformation
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\MetaInformation
 */
vDesk.MetaInformation.Mask = function Mask(ID = null, Name = vDesk.Locale.MetaInformation.NewMask, Rows = []) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Name, Type.String, "Name");
    Ensure.Parameter(Rows, Array, "Rows");

    Object.defineProperties(this, {
        ID:   {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Name: {
            enumerable: true,
            get:        () => Name,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                Name = Value;
            }
        },
        Rows: {
            enumerable: true,
            get:        () => Rows,
            set:        Value => {
                Ensure.Property(Value, Array, "Rows");
                Rows = Value;
            }
        }
    });
};

/**
 * Factory method that creates a Mask from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Mask.
 * @return {vDesk.MetaInformation.Mask} A Mask filled with the provided data.
 */
vDesk.MetaInformation.Mask.FromDataView = function(DataView) {
    Ensure.Parameter(DataView, Type.Object, "DataView");
    return new vDesk.MetaInformation.Mask(
        DataView?.ID ?? null,
        DataView?.Name ?? vDesk.Locale.MetaInformation.NewMask,
        DataView?.Rows?.map(Row => vDesk.MetaInformation.Mask.Row.FromDataView(Row)) ?? []
    );
};