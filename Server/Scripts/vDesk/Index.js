window.addEventListener("load", () => {

    const Features = document.getElementById("Features");
    const FeaturesOffset = Features.offsetTop;
    const FeatureSlideShow = new SlideShow(document.getElementById("FeatureSlideShow"));
    FeatureSlideShow.Pause();
    const CodeSlideShow = new SlideShow(document.getElementById("CodeSlideShow"));
    CodeSlideShow.Pause();


    const Development = document.getElementById("Development");


    const Height = window.innerHeight;

    const Technology = document.getElementById("Technology");
    const TechnologyOffset = Technology.offsetTop;
    const Customization = document.getElementById("Customization");
    const CustomizationOffset = Customization.offsetTop;

    const DevelopmentOffset = Development.offsetTop;

    const OnScrollFeatures = () => {
        let Offset = window.scrollY + Height;
        if(Offset > FeaturesOffset) {
            Features.classList.toggle("Left", true);
            FeatureSlideShow.Play();
            window.removeEventListener("scroll", OnScrollFeatures);
            window.addEventListener("scroll", OnScrollTechnology);
        }
    };
    window.addEventListener("scroll", OnScrollFeatures);
    const OnScrollTechnology = () => {
        let Offset = window.scrollY + Height;
        if(Offset > TechnologyOffset) {
            Technology.classList.toggle("Paused", false);
            Array.from(Technology.getElementsByClassName("Paused"))
                .forEach(Node => Node.classList.toggle("Paused", false));
            window.removeEventListener("scroll", OnScrollTechnology);
            window.addEventListener("scroll", OnScrollCustomization);
        }
    };
    const OnScrollCustomization = () => {
        let Offset = window.scrollY + Height;
        if(Offset > CustomizationOffset) {
            Customization.classList.toggle("Paused", false);
            Array.from(Customization.getElementsByClassName("Paused"))
                .forEach(Node => Node.classList.toggle("Paused", false));
            window.removeEventListener("scroll", OnScrollCustomization);
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