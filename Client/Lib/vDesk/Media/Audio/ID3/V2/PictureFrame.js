"use strict";
/**
 * Initializes a new instance of the PictureFrame class.
 * @class Represents an image-containing dataframe of ID3v2.x-Tags.
 * @see http://id3.org/id3v2.3.0#Attached_picture
 * @param {String} [ID=""] The ID of the PictureFrame.
 * @param {Number} [StatusFlags=0] The status flags of the PictureFrame.
 * @param {Number} [EncodingFlags=0] The encoding flags of the PictureFrame.
 * @param {Uint8Array} Data The data of the PictureFrame.
 * @property {String} ID Gets or sets the ID of the PictureFrame.
 * @property {Number} StatusFlags Gets or sets the status flags of the PictureFrame.
 * @property {Number} EncodingFlags Gets or sets the encoding flags of the PictureFrame.
 * @property {Blob} Data Gets the data of the PictureFrame.
 * @property {String} Encoding Gets the encoding of the data of the PictureFrame.
 * @property {String} MimeType Gets the MIMEType of the data of the PictureFrame.
 * @property {Number} ImageType Gets the type of the image of the PictureFrame.
 * @property {String} Description Gets the description-text of the PictureFrame.
 * @memberOf vDesk.Media.Audio.ID3.V2
 * @augments vDesk.Media.Audio.ID3.V2.Frame
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Audio.ID3.V2.PictureFrame = function PictureFrame(ID, StatusFlags, EncodingFlags, Data) {
    this.Extends(vDesk.Media.Audio.ID3.V2.Frame, ID, StatusFlags, EncodingFlags);

    Object.defineProperties(this, {
        Data:        {
            get:        () => new Blob([Data.slice(DescriptionIndex + 1)], {type: MimeType}),
            enumerable: true
        },
        Encoding:    {
            get:        () => Encoding,
            enumerable: true
        },
        MimeType:    {
            get:        () => MimeType,
            enumerable: true
        },
        ImageType:   {
            get:        () => ImageType,
            enumerable: true
        },
        Description: {
            get:        () => Description,
            enumerable: true
        }
    });

    /**
     * The encoding of the description of the PictureFrame.
     * @type {String}
     */
    const Encoding = Data[0] === 0x00
                     ? vDesk.Media.Audio.ID3.V2.Frame.Encoding.LATIN1
                     : vDesk.Media.Audio.ID3.V2.Frame.Encoding.UTF16;

    /**
     * The TextDecoder of the PictureFrame.
     * @type {TextDecoder}
     */
    const Decoder = new TextDecoder(Encoding);

    /**
     * The index of the MIME-Type of the PictureFrame.
     * @type {Number}
     */
    const MimeIndex = Data.indexOf(0x00, 1);

    /**
     * The MIME-Type of the image of the PictureFrame.
     * @type {String}
     */
    const MimeType = Decoder.decode(Data.slice(1, MimeIndex));

    /**
     * The type of the image of the PictureFrame.
     * @type {Number}
     */
    const ImageType = Data[MimeIndex + 1];

    /**
     * The index of the description of the PictureFrame.
     * @type {Number}
     */
    const DescriptionIndex = Data.indexOf(0x00, MimeIndex + 2);

    /**
     * The description of the PictureFrame.
     * @type {String}
     */
    const Description = Decoder.decode(Data.slice(MimeIndex + 2, DescriptionIndex));
};