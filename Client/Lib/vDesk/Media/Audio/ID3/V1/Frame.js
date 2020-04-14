"use strict";
/**
 * Initializes a new instance of the Frame class.
 * @class Represents dataframe of ID3v1.x-Tags acting like dataframes specified for the 2.x version.
 * @param {String} [ID=""] The ID of the frame.
 * @param {String} [Data=""] The data of the frame.
 * @property {String} ID Gets or sets the ID of the frame.
 * @property {String} Data Gets or sets the data of the frame.
 * @implements vDesk.Media.Audio.ID3.IFrame
 * @memberOf vDesk.Media.Audio.ID3.V1
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.V1.Frame = function(ID = "", Data = "") {
    Object.defineProperties(this, {
        ID:   {
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.String, "ID");
                ID = Value;
            },
            enumerable: true
        },
        Data: {
            get:        () => Data,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Data");
                Data = Value;
            },
            enumerable: true
        }
    });
};

vDesk.Media.Audio.ID3.V1.Frame.Implements(vDesk.Media.Audio.ID3.IFrame);