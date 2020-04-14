"use strict";
/**
 * Contains functions for reading ID3v2-tags of audiofiles.
 * @namespace V2
 * @memberOf vDesk.Media.Audio.ID3
 */
vDesk.Media.Audio.ID3.V2 = {

    /**
     * Parses the ID3v2-tag of an audio file.
     * @param {DataView} View The view on the data of the audio file to parse.
     * @param {Boolean} [SkipEmptyFrames=false] Determines whether frames with empty data will be excluded from the ID3Tag.
     * @return {vDesk.Media.Audio.ID3.Tag} An ID3Tag containing the information of the specified audio file.
     */
    Parse: function(View, SkipEmptyFrames = false) {
        Ensure.Parameter(View, DataView, "View");
        Ensure.Parameter(SkipEmptyFrames, Type.Boolean, "SkipEmptyFrames");

        const Flags = View.getUint8(5);
        const Size = View.getUint32(6);
        const Frames = [];

        //Loop through the audio file and parse data frames.
        for(let Offset = 10; Offset < Size;) {

            //Check if the end of frames has been reached.
            if(View.getUint32(Offset) === 0x00) {
                break;
            }

            //Get frame ID.
            let ID = "";
            for(let i = 0; i < 4; i++) {
                ID = ID.concat(String.fromCharCode(View.getUint8(Offset++)));
            }

            //Get frame size.
            const FrameSize = View.getUint32(Offset);
            Offset += 4;

            //Get frame.
            let FrameType = null;
            switch(true) {
                case ID === "APIC": //Attached picture frame.
                    FrameType = vDesk.Media.Audio.ID3.V2.PictureFrame;
                    break;
                case ID[0] === "T": //Text frame.
                    FrameType = vDesk.Media.Audio.ID3.V2.TextFrame;
                    break;
                case ID === "PRIV": //Private frame.
                    FrameType = vDesk.Media.Audio.ID3.V2.PrivateFrame;
                    break;
                case ID[0] === "W": //URL frame.
                    FrameType = vDesk.Media.Audio.ID3.V2.URLFrame;
                    break;
                case ID === "COMM": //Comment frame.
                    FrameType = vDesk.Media.Audio.ID3.V2.CommentFrame;
                    break;
                default:
                    //Skip unknown frame.
                    Offset += FrameSize;
                    continue;
            }

            //Create frame.
            const Frame = new FrameType(
                ID,
                View.getUint8(Offset++),
                View.getUint8(Offset++),
                new Uint8Array(View.buffer, Offset, FrameSize)
            );

            //Increase offset to next frame.
            Offset += FrameSize;

            //CHeck if empty frames should get skipped and if the frame has no data.
            if(SkipEmptyFrames && Frame.data.length === 0) {
                continue;
            }

            //Append frame.
            Frames.push(Frame);

        }

        return new vDesk.Media.Audio.ID3.Tag(
            `2.${View.getUint8(3)}.${View.getUint8(4)}`,
            {
                Unsynchronisation: Flags & 1,
                ExtendedHeader:    Flags & 2,
                Experimental:      Flags & 4
            },
            Frames);
    }
};