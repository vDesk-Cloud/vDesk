"use strict";
/**
 * Contains functions for reading ID3v1-tags of audiofiles.
 * @namespace V1
 * @memberOf vDesk.Media.Audio.ID3
 */
vDesk.Media.Audio.ID3.V1 = {

    /**
     * Parses the ID3v1-tag of an audiofile.
     * @param {DataView} View The view on the data of the audiofile to parse.
     * @param {Boolean} [SkipEmptyFrames=false] Determines whether frames with empty data will be excluded from the ID3Tag.
     * @return {vDesk.Media.Audio.ID3.Tag} An ID3Tag containing the information of the specified audiofile.
     */
    Parse:  function(View, SkipEmptyFrames = false) {
        Ensure.Parameter(View, DataView, "View");
        Ensure.Parameter(SkipEmptyFrames, Type.Boolean, "SkipEmptyFrames");

        const OffsetTag = View.byteLength - 128;
        const OffsetComment = OffsetTag + 97;

        const Decoder = new TextDecoder(vDesk.Media.Audio.ID3.V2.Frame.Encoding.LATIN1);
        let Commentlength = 30;
        let Frames = [];
        const FilterNullBytes = Value => Value !== 0x00;

        //Get title.
        Frames.push(new vDesk.Media.Audio.ID3.V1.Frame("TIT2", Decoder.decode(
            new Uint8Array(View.buffer, OffsetTag + 3, 30).filter(FilterNullBytes))));

        //Get author.
        Frames.push(new vDesk.Media.Audio.ID3.V1.Frame("TPE1", Decoder.decode(
            new Uint8Array(View.buffer, OffsetTag + 33, 30).filter(FilterNullBytes))));

        //Get album.
        Frames.push(new vDesk.Media.Audio.ID3.V1.Frame("TALB", Decoder.decode(
            new Uint8Array(View.buffer, OffsetTag + 63, 30).filter(FilterNullBytes))));

        //Get year of release.
        Frames.push(new vDesk.Media.Audio.ID3.V1.Frame("TYER", Decoder.decode(
            new Uint8Array(View.buffer, OffsetTag + 93, 4).filter(FilterNullBytes))));

        //Check if a tracknumber exists (ID3v1.1).
        if(View.getUint8(OffsetComment + 29) === 0x00) {
            Frames.push(new vDesk.Media.Audio.ID3.V1.Frame("TRCK", View.getUint8(OffsetComment + 30).toString()));
            Commentlength = 28;
        }

        //Get comment.
        Frames.push(new vDesk.Media.Audio.ID3.V1.Frame("COMM", Decoder.decode(
            new Uint8Array(View.buffer, OffsetComment, Commentlength).filter(FilterNullBytes))));

        //Check if empty frames should get skipped.
        if(SkipEmptyFrames) {
            Frames = Frames.filter(Frame => typeof Frame.Data === Type.String && Frame.Data.length > 0);
        }

        //Get genre.
        Frames.push(new vDesk.Media.Audio.ID3.V1.Frame("TCON", this.Genres[View.getUint8(OffsetTag + 127)]));

        return new vDesk.Media.Audio.ID3.Tag(`1.${Commentlength < 30 ? "1" : "0"}.0`, {}, Frames);
    },
    /**
     * Enumeration of all possible music-genres of the ID3v1-specification.
     * @see https://de.wikipedia.org/wiki/Liste_der_ID3v1-Genres
     * @readonly
     * @enum {String}
     */
    Genres: [
        "Blues",
        "Classic Rock",
        "Country",
        "Dance",
        "Disco",
        "Funk",
        "Grunge",
        "Hip-Hop",
        "Jazz",
        "Metal",
        "New Age",
        "Oldies",
        "Other",
        "Pop",
        "Rhythm and Blues",
        "Rap",
        "Reggae",
        "Rock",
        "Techno",
        "Industrial",

        "Alternative",
        "Ska",
        "Death Metal",
        "Pranks",
        "Soundtrack",
        "Euro-Techno",
        "Ambient",
        "Trip-Hop",
        "Vocal",
        "Jazz & Funk",
        "Fusion",
        "Trance",
        "Classical",
        "Instrumental",
        "Acid",
        "House",
        "Game",
        "Sound Clip",
        "Gospel",
        "Noise",

        "Alternative Rock",
        "Bass",
        "Soul",
        "Punk",
        "Space",
        "Meditative",
        "Instrumental Pop",
        "Instrumental Rock",
        "Ethnic",
        "Gothic",
        "Darkwave",
        "Techno-Industrial",
        "Electronic",
        "Pop-Folk",
        "Eurodance",
        "Dream",
        "Southern Rock",
        "Comedy",
        "Cult",
        "Gangsta",

        "Top 40",
        "Christian Rap",
        "Pop/Funk",
        "Jungle",
        "Native US",
        "Cabaret",
        "New Wave",
        "Psychedelic",
        "Rave",
        "Showtunes",
        "Trailer",
        "Lo-Fi",
        "Tribal",
        "Acid Punk",
        "Acid Jazz",
        "Polka",
        "Retro",
        "Musical",
        "Rock ’n’ Roll",
        "Hard Rock"
    ]
};