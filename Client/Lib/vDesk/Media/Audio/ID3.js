"use strict";
/**
 * Contains functions for reading ID3-tags of audio files.
 * @namespace ID3
 * @memberOf vDesk.Media.Audio
 */
vDesk.Media.Audio.ID3 = {
    /**
     * Parses the ID3-tag of an audio file.
     * @param {ArrayBuffer} Buffer The buffer of the data of the audio file to parse.
     * @param {Boolean} [SkipEmptyFrames=false] Determines whether frames with empty data will be excluded from the ID3Tag.
     * @param {Number} [Version=2] The ID3 version to use.
     * @return {vDesk.Media.Audio.ID3.Tag} An ID3Tag containing the information of the specified audio file.
     */
    Parse:       function(Buffer, SkipEmptyFrames = false, Version = 2) {
        Ensure.Parameter(Buffer, ArrayBuffer, "Buffer");
        Ensure.Parameter(SkipEmptyFrames, Type.Boolean, "SkipEmptyFrames");
        Ensure.Parameter(Version, Type.Number, "Version");

        if(!(Buffer instanceof ArrayBuffer)) {
            throw new TypeError("Argument for parameter 'Buffer' must be an instance of ArrayBuffer.");
        }

        const View = new DataView(Buffer);

        switch(true) {
            case Version <= 1 && this.ContainsTag(View, 1):
                return this.V1.Parse(View, SkipEmptyFrames);
            case Version >= 2 && this.ContainsTag(View, 2):
                return this.V2.Parse(View, SkipEmptyFrames);
            default:
                return new vDesk.Media.Audio.ID3.Tag();
        }
    },
    /**
     * Enumeration of default DataFrames which are commonly used in audio players.
     * @readonly
     * @enum {String}
     */
    Frames:      {
        APIC: "Attached picture",
        TIT2: "Title",
        TPE1: "Author",
        TALB: "Album",
        TYER: "Year of release",
        COMM: "Comment",
        TCON: "Genre",
        TCOM: "Componist",
        TRCK: "Tracknumber"
    },
    /**
     * Checks a file for the existence of an ID3 version-specific Tag-identifier.
     * @param {DataView} View The DataView of the file to check.
     * @param {Number} Version The ID3 version to check for.
     * @return {Boolean} True if the specified file contains a valid Tag-identifier; otherwise, false.
     */
    ContainsTag: function(View, Version) {
        switch(Version) {
            case 1:
                //Check if the first 3 bytes at the end of the file, subtracting tag-size (128byte) contains the word 'TAG'.
                return View.getUint8(View.byteLength - 128) === 84
                       && View.getUint8(View.byteLength - 127) === 65
                       && View.getUint8(View.byteLength - 126) === 71;
            case 2:
                //Check if the first 3 bytes contain the word 'ID3'.
                return View.getUint8(0) === 73
                       && View.getUint8(1) === 68
                       && View.getUint8(2) === 51;
            default:
                return false;
        }
    }
};