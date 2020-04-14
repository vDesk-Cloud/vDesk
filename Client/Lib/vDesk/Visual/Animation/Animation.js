/**
 * Collection of animations.
 * @namespace Animation
 * @memberOf vDesk.Visual
 */
vDesk.Visual.Animation = {
    /**
     * Framerate of 60 frames per second.
     * @constant
     * @type {Number}
     */
    FPS60:            60,
    /**
     * Framerate of 30 frames per second.
     * @constant
     * @type {Number}
     */
    FPS30:            30,
    /**
     * Framerate for the video-specification. Equals 30 frames per second.
     * @constant
     * @type {Number}
     */
    FPSVideo:         this.FPS30,
    /**
     * Framerate for the movie-specification. Equals 24 frames per second.
     * @constant
     * @type {Number}
     */
    FPSMovie:         24,
    /**
     * Resizes the height of a DOM-Node.
     * @param {HTMLElement} Target The target DOM-Node to animate.
     * @param {Number} From The initial height of the target DOM-Node.
     * @param {Number} To The final height of the target DOM-Node.
     * @param {Number} Duration The duration of the animation.
     * @param {Function} [Callback=null] The callback to execute after the animation has been completed.
     * @param {Number} [FPS=vDesk.Visual.Animation.FPS60] The frames per second the animation will be rendered at.
     */
    ResizeVertical:   function(Target, From, To, Duration, Callback = null, FPS = vDesk.Visual.Animation.FPS60) {
        Ensure.Parameter(Target, HTMLElement, "Target");
        Ensure.Parameter(From, Type.Number, "From");
        Ensure.Parameter(To, Type.Number, "To");
        Ensure.Parameter(Duration, Type.Number, "Duration");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);
        Ensure.Parameter(FPS, Type.Number, "FPS");

        //Get animation range.
        const Difference = To - From;
        //Get pixels to increase/decrease every frame.
        const Step = Difference / (Duration / 60);
        //Set progress to starting value.
        let Progress = From;

        const Execute = () => {
            //Render every 16 milliseconds 1 frame.
            window.setTimeout(
                () => {
                    //Update progress.
                    Progress = Number(From += Step).toFixed(0);
                    //Update the value of the target property.
                    Target.style.height = Progress + "px";

                    //Check if the target value has not been reached yet.
                    if(Step > 0 && Progress < To || Step < 0 && Progress > To) {
                        window.requestAnimationFrame(Execute);
                    } else {
                        //Else set target property to the target value and execute any callback.
                        Target.style.height = To + "px";
                        if(Callback !== null) {
                            Callback();
                        }
                    }
                },
                1000 / FPS
            );
        };

        //Start the animation.
        window.requestAnimationFrame(Execute);
    },
    /**
     * Resizes the width of a DOM-Node.
     * @param {HTMLElement} Target The target DOM-Node to animate.
     * @param {Number} From The initial width of the target DOM-Node.
     * @param {Number} To The final width of the target DOM-Node.
     * @param {Number} Duration The duration of the animation.
     * @param {Function} [Callback=null] The callback to execute after the animation has been completed.
     * @param {Number} [FPS=vDesk.Visual.Animation.FPS60] The frames per second the animation will be rendered at.
     */
    ResizeHorizontal: function(Target, From, To, Duration, Callback = null, FPS = vDesk.Visual.Animation.FPS60) {
        Ensure.Parameter(Target, HTMLElement, "Target");
        Ensure.Parameter(From, Type.Number, "From");
        Ensure.Parameter(To, Type.Number, "To");
        Ensure.Parameter(Duration, Type.Number, "Duration");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);
        Ensure.Parameter(FPS, Type.Number, "FPS");

        //Get animation range.
        const Difference = To - From;
        //Get pixels to increase/decrease every frame.
        const Step = Difference / (Duration / 60);
        //Set progress to starting value.
        let Progress = From;

        const Execute = () => {
            //Render every 16 milliseconds 1 frame.
            window.setTimeout(
                () => {
                    //Update progress.
                    Progress = Number(From += Step).toFixed(0);
                    //Update the value of the target property.
                    Target.style.width = Progress + "px";

                    //Check if the target value has not been reached yet.
                    if(Step > 0 && Progress < To || Step < 0 && Progress > To) {
                        window.requestAnimationFrame(Execute);
                    } else {
                        //Else set target property to the target value and execute any callback.
                        Target.style.width = To + "px";
                        if(Callback !== null) {
                            Callback();
                        }
                    }
                },
                1000 / FPS
            );
        };

        //Start the animation.
        window.requestAnimationFrame(Execute);
    },
    /**
     * Resizes the height and width of a DOM-Node.
     * @param {HTMLElement} Target The target DOM-Node to animate.
     * @param {Number} FromHeight The initial height of the target DOM-Node.
     * @param {Number} ToHeight The final height of the target DOM-Node.
     * @param {Number} FromWidth The initial width of the target DOM-Node.
     * @param {Number} ToWidth The final width of the target DOM-Node.
     * @param {Number} Duration The duration of the animation.
     * @param {Function} [Callback=null] The callback to execute after the animation has been completed.
     * @param {Number} [FPS=vDesk.Visual.Animation.FPS60] The frames per second the animation will be rendered at.
     */
    Resize:           function(Target, FromHeight, ToHeight, FromWidth, ToWidth, Duration, Callback = null, FPS = vDesk.Visual.Animation.FPS60) {
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        let VerticalCallBackExecuted = false;
        let HorizontalCallBackExecuted = false;

        this.ResizeVertical(
            Target,
            FromHeight,
            ToHeight,
            Duration,
            () => {
                VerticalCallBackExecuted = true;
                if(
                    VerticalCallBackExecuted
                    && HorizontalCallBackExecuted
                    && Callback !== null
                ) {
                    Callback();
                }
            },
            FPS
        );

        this.ResizeHorizontal(
            Target,
            FromWidth,
            ToWidth,
            Duration,
            () => {
                HorizontalCallBackExecuted = true;
                if(
                    HorizontalCallBackExecuted
                    && VerticalCallBackExecuted
                    && Callback !== null
                ) {
                    Callback();
                }
            },
            FPS
        );

    },
    /**
     * Pops up a DOM-Node.
     * Resizes a specified DOM-Node from 0 height and width to a specified height and width and fades it into visibility.
     * @param {HTMLElement} Target The target DOM-Node to animate.
     * @param {Number} Height The final height of the DOM-Node.
     * @param {Number} Width The final width of the DOM-Node.
     * @param {Number} Duration The duration of the animation.
     * @param {Function} [Callback=null] The callback to execute after the animation has been completed.
     * @param {Number} [FPS=vDesk.Visual.Animation.FPS60] The frames per second the animation will be rendered at.

     */
    Pop:              function(Target, Height, Width, Duration, Callback = null, FPS = vDesk.Visual.Animation.FPS60) {
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        let VerticalCallBackExecuted = false;
        let HorizontalCallBackExecuted = false;
        let FadeInCallBackExecuted = false;

        this.ResizeVertical(
            Target,
            0,
            Height,
            Duration,
            () => {
                VerticalCallBackExecuted = true;
                if(
                    VerticalCallBackExecuted
                    && HorizontalCallBackExecuted
                    && FadeInCallBackExecuted
                    && Callback !== null
                ) {
                    Callback();
                }
            },
            FPS
        );

        this.ResizeHorizontal(
            Target,
            0,
            Width,
            Duration,
            () => {
                HorizontalCallBackExecuted = true;
                if(
                    HorizontalCallBackExecuted
                    && VerticalCallBackExecuted
                    && FadeInCallBackExecuted
                    && Callback !== null
                ) {
                    Callback();
                }
            },
            FPS
        );

        this.FadeIn(
            Target,
            Duration,
            () => {
                FadeInCallBackExecuted = true;
                if(
                    FadeInCallBackExecuted
                    && HorizontalCallBackExecuted
                    && VerticalCallBackExecuted
                    && Callback !== null
                ) {
                    Callback();
                }
            },
            FPS
        );

    },
    /**
     * Animates a css-property of a DOM-Node.
     * @param {HTMLElement} Target The target DOM-Node to animate.
     * @param {String} Property The target property to animate.
     * @param {Number} From The start value of the target property.
     * @param {Number} To The target value of the target property.
     * @param {Number} Duration The duration of the animation.
     * @param {Function} [Callback=null] The callback to execute after the animation has been completed.
     * @param {Number} [FPS=vDesk.Visual.Animation.FPS60] The frames per second the animation will be rendered at.
     */
    Animate:          function(Target, Property, From, To, Duration, Callback = null, FPS = vDesk.Visual.Animation.FPS60) {
        Ensure.Parameter(Target, HTMLElement, "Target");
        Ensure.Parameter(Property, Type.String, "Property");
        Ensure.Parameter(From, Type.Number, "From");
        Ensure.Parameter(To, Type.Number, "To");
        Ensure.Parameter(Duration, Type.Number, "Duration");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);
        Ensure.Parameter(FPS, Type.Number, "FPS");

        //Get animation range.
        const Difference = To - From;
        //Get pixels to increase/decrease every frame.
        const Step = Difference / (Duration / 60);
        //Set progress to starting value.
        let Progress = From;

        const Execute = () => {
            //Render every 16 milliseconds 1 frame.
            window.setTimeout(
                () => {
                    //Update progress.
                    Progress = Number(From += Step).toFixed(0);
                    //Update the value of the target property.
                    Target.style[Property] = Progress + "px";

                    //Check if the target value has not been reached yet.
                    if(Step > 0 && Progress < To || Step < 0 && Progress > To) {
                        window.requestAnimationFrame(Execute);
                    } else {
                        //Else set target property to the target value and execute any callback.
                        Target.style[Property] = To + "px";
                        if(Callback !== null) {
                            Callback();
                        }
                    }
                },
                1000 / FPS
            );
        };

        //Start the animation.
        window.requestAnimationFrame(Execute);
    },
    /**
     * Fades a DOM-Node from a specified opacity into a specified target opacity.
     * @param {HTMLElement} Target The target DOM-Node to fade.
     * @param {Number} From The staring opacity of the DOM-Node.
     * @param {Number} To The target opacity of the DOM-Node.
     * @param {Number} Duration The duration of the animation in milliseconds.
     * @param {Function} [Callback=null] The callback to execute after the animation has been completed.
     * @param {Number} [FPS=vDesk.Visual.Animation.FPS60] The frames per second the animation will be rendered at.
     */
    FadeTo:           function(Target, From, To, Duration, Callback = null, FPS = vDesk.Visual.Animation.FPS60) {
        Ensure.Parameter(Target, HTMLElement, "Target");
        Ensure.Parameter(From, Type.Number, "From");
        Ensure.Parameter(To, Type.Number, "To");
        Ensure.Parameter(Duration, Type.Number, "Duration");
        Ensure.Parameter(Callback, Type.Function, "Callback", true);
        Ensure.Parameter(FPS, Type.Number, "FPS");

        //Get animation range.
        const Difference = To - From;
        //Get pixels to increase/decrease every frame.
        const Step = Difference / (Duration / 60);
        //Set progress to starting value.
        let Progress = From;

        const Execute = () => {
            //Render every 16 milliseconds 1 frame.
            window.setTimeout(
                () => {
                    //Update progress.
                    Progress += Step;
                    //Update the value of the target property.
                    Target.style.opacity = Progress;

                    //Check if the target value has not been reached yet.
                    if(Step > 0 && Progress < To || Step < 0 && Progress > To) {
                        window.requestAnimationFrame(Execute);
                    } else {
                        //Else set target property to the target value and execute any callback.
                        Target.style.opacity = To.toString();
                        if(Callback !== null) {
                            Callback();
                        }
                    }
                },
                1000 / FPS
            );
        };

        //Start the animation.
        window.requestAnimationFrame(Execute);
    },
    /**
     * Fades a DOM-Node into visibility.
     * @param {HTMLElement} Target The target DOM-Node to fade in.
     * @param {Number} Duration The duration of the animation in milliseconds.
     * @param {Function} [Callback=null] The callback to execute after the animation has been completed.
     * @param {Number} [FPS=vDesk.Visual.Animation.FPS60] The frames per second the animation will be rendered at.
     */
    FadeIn:           function(Target, Duration, Callback = null, FPS = vDesk.Visual.Animation.FPS60) {
        return this.FadeTo(Target, 0, 1, Duration, Callback, FPS);
    },
    /**
     * Fades a DOM-Node out of visibility.
     * @param {HTMLElement} Target The target DOM-Node to fade in.
     * @param {Number} Duration The duration of the animation in milliseconds.
     * @param {Function} [Callback=null] The callback to execute after the animation has been completed.
     * @param {Number} [FPS=vDesk.Visual.Animation.FPS60] The frames per second the animation will be rendered at.
     */
    FadeOut:          function(Target, Duration, Callback = null, FPS = vDesk.Visual.Animation.FPS60) {
        return this.FadeTo(Target, 1, 0, Duration, Callback, FPS);
    }

};