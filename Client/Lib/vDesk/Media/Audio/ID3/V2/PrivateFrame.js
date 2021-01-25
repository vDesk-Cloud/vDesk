"use strict";
/**
 * Initializes a new instance of the PrivateFrame class.
 * @class Represents a text-based dataframe of ID3v2.x-Tags.
 * @see http://id3.org/id3v2.3.0#Text_information_frames
 * @param {String} [ID=""] The ID of the PrivateFrame.
 * @param {Number} [StatusFlags=0] The status flags of the PrivateFrame.
 * @param {Number} [EncodingFlags=0] The encoding flags of the PrivateFrame.
 * @param {Uint8Array} Data The data of the PrivateFrame.
 * @property {String} ID Gets or sets the ID of the PrivateFrame.
 * @property {Number} StatusFlags Gets or sets the status flags of the PrivateFrame.
 * @property {Number} EncodingFlags Gets or sets the encoding flags of the PrivateFrame.
 * @property {String} Data Gets the data of the PrivateFrame.
 * @property {String} OwnerIdentifier Gets the owner identifier of the data of the PrivateFrame.
 * @memberOf vDesk.Media.Audio.ID3.V2
 * @augments vDesk.Media.Audio.ID3.V2.Frame
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.V2.PrivateFrame = function PrivateFrame(ID, StatusFlags, EncodingFlags, Data) {
    this.Extends(vDesk.Media.Audio.ID3.V2.Frame, ID, StatusFlags, EncodingFlags);

    Object.defineProperties(this, {
        Data:            {
            get:        () => Decoder.decode(Data.slice(OwnerIndex + 1)),
            enumerable: true
        },
        OwnerIdentifier: {
            get:        () => OwnerIdentifier,
            enumerable: true
        }
    });

    /**
     * The TextDecoder of the PrivateFrame.
     * @type {TextDecoder}
     */
    const Decoder = new TextDecoder(vDesk.Media.Audio.ID3.V2.Frame.Encoding.LATIN1);

    /**
     * The index of the termination null-byte of the owner identifier of the PrivateFrame.
     * @type {Number}
     */
    const OwnerIndex = Data.indexOf(0x00);

    /**
     * The owner identifier of the PrivateFrame.
     * @type {String}
     */
    const OwnerIdentifier = Decoder.decode(Data.slice(0, OwnerIndex));

};