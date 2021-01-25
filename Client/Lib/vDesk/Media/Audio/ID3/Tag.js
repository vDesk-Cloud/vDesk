"use strict";
/**
 * Initializes a new instance of the Tag class.
 * @class Represents the information of an ID3-Tag of an audio file.
 * @param {String} [Version="0.0.0"] The version of the tag.
 * @param {Object} [Flags={}] The flags of the tag.
 * @param {Array<vDesk.Media.Audio.ID3.IFrame>} [Frames=[]] The data frames of the tag.
 * @property {String} Version Gets or sets the version of the tag.
 * @property {Object} Version Gets or sets the flags of the tag.
 * @property {Array<vDesk.Media.Audio.ID3.IFrame>} Frames Gets or sets the data frames of the tag.
 * @memberOf vDesk.Media.Audio.ID3
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.Tag = function(Version = "0.0.0", Flags = {}, Frames = []) {
    Object.defineProperties(this, {
        Version: {
            get:        () => Version,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Version");
                Version = Value;
            },
            enumerable: true
        },
        Flags:   {
            get:        () => Version,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Flags");
                Version = Value;
            },
            enumerable: true
        },
        Frames:  {
            get:        () => Frames,
            set:        Value => {
                Ensure.Property(Value, Array, "Frames");
                Frames = Value;
            },
            enumerable: true
        }
    });
};