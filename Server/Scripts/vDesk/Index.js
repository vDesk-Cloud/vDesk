window.addEventListener("load", () => {

    const FeatureSlideShow = new SlideShow(document.getElementById("FeatureSlideShow"));
    FeatureSlideShow.Pause();
    const CodeSlideShow = new SlideShow(document.getElementById("CodeSlideShow"));
    CodeSlideShow.Pause();

    const Features = document.getElementById("Features");
    const Technology = document.getElementById("Technology");
    const Customization = document.getElementById("Customization");
    const Development = document.getElementById("Development");
    const Observer = new IntersectionObserver(
        Entries => Entries.forEach(Entry => {
            if (Entry.isIntersecting) {
                switch (Entry.target) {
                    case Features:
                        Features.classList.toggle("Left", true);
                        FeatureSlideShow.Play();
                        Observer.unobserve(Features);
                        break;
                    case Technology:
                        Technology.classList.toggle("Right", true);
                        Array.from(Technology.getElementsByClassName("Paused"))
                            .forEach(Node => Node.classList.toggle("Paused", false));
                        Observer.unobserve(Technology);
                        break;
                    case Customization:
                        Customization.classList.toggle("Left", true);
                        Array.from(Customization.getElementsByClassName("Paused"))
                            .forEach(Node => Node.classList.toggle("Paused", false));
                        Observer.unobserve(Customization);
                        break;
                    case Development:
                        Development.classList.toggle("Right", true);
                        CodeSlideShow.Play();
                        Observer.disconnect();
                }
            }
        }),
        {
            root:       null,
            rootMargin: "0px",
            threshold:  0.1
        }
    );
    Observer.observe(Features);
    Observer.observe(Technology);
    Observer.observe(Customization);
    Observer.observe(Development);
});