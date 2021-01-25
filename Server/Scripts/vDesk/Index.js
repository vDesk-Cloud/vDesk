window.addEventListener("load", () => {

    const Features = document.getElementById("Features");
    const FeaturesOffset = Features.offsetTop;
    const FeatureSlideShow = new SlideShow(document.getElementById("FeatureSlideShow"));
    FeatureSlideShow.Pause();
    const CodeSlideShow = new SlideShow(document.getElementById("CodeSlideShow"));
    CodeSlideShow.Pause();


    const Development = document.getElementById("Development");


    const Height = window.innerHeight;

    const Requirements = document.getElementById("Requirements");
    const RequirementsOffset = Requirements.offsetTop;
    const Customizable = document.getElementById("Customizable");
    const CustomizableOffset = Customizable.offsetTop;

    const DevelopmentOffset = Development.offsetTop;

    const OnScrollFeatures = () => {
        let Offset = window.scrollY + Height;
        if(Offset > FeaturesOffset) {
            Features.classList.toggle("Left", true);
            FeatureSlideShow.Play();
            window.removeEventListener("scroll", OnScrollFeatures);
            window.addEventListener("scroll", OnScrollRequirements);
        }
    };
    window.addEventListener("scroll", OnScrollFeatures);
    const OnScrollRequirements = () => {
        let Offset = window.scrollY + Height;
        if(Offset > RequirementsOffset) {
            Requirements.classList.toggle("Paused", false);
            Array.from(Requirements.getElementsByClassName("Paused"))
                .forEach(Node => Node.classList.toggle("Paused", false));
            window.removeEventListener("scroll", OnScrollRequirements);
            window.addEventListener("scroll", OnScrollCustomizable);
        }
    };
    const OnScrollCustomizable = () => {
        let Offset = window.scrollY + Height;
        if(Offset > CustomizableOffset) {
            Customizable.classList.toggle("Paused", false);
            Array.from(Customizable.getElementsByClassName("Paused"))
                .forEach(Node => Node.classList.toggle("Paused", false));
            window.removeEventListener("scroll", OnScrollCustomizable);
            window.addEventListener("scroll", OnScrollDevelopment);
        }
    };
    const OnScrollDevelopment = () => {
        let Offset = window.scrollY + Height;
        if(Offset > DevelopmentOffset) {
            Development.classList.toggle("Right", true);
            CodeSlideShow.Play();
            window.removeEventListener("scroll", OnScrollDevelopment);
        }
    };
});