<?php

use vDesk\Pages\Functions;

?>
<article class="Donate">
    <header>
        <h2>About</h2>
    </header>
    <section>
        <h3>What is vDesk?</h3>
        <p>
            Well... this question I would've answered around 10 years ago with
            <span style="font-style: italic">"A simple cloud with a bit drag&drop support and folder navigation"</span>,<br>
            but in the meantime it would be easier to ask what it's not.
        </p>
        <p>
            Today, the most fitting term would be a self hosted "groupware" written in PHP and JavaScript with a configurable featureset powered by vDesk's package system.
        </p>
        <p>
            At least I can tell it lets me access my archived files, important dates and contacts and quickly written down notes from anywhere hosted on a RasPi 4 with a pair of SSDs attached.
            <br>It even went that far, that I started to control and survey my paludaria with an unreleased <a href="https://github.com/vDesk-Cloud/vDesk/tree/GPIO-1.0.0">GPIO</a> package
            distributed over a small self written <a href="https://github.com/vDesk-Cloud/vDesk/tree/Relay-1.0.0">message queue system</a>,
            both driven by the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Machines") ?>">Machines</a> package as shown on the picture below.
            (The GPIO package will be released when all general types of sensors are supported)
        </p>
        <aside class="Image" style="text-align: center" onclick="this.classList.toggle('Fullscreen')">
            <img style="max-width: 50%" src="<?= Functions::Image("vDesk", "GPIO.jpg") ?>" class="Author" alt="Author">
        </aside>
    </section>
    <section>
        <h3>Who the heck made all this stuff?</h3>
        <p>
            <img style="max-width: 250px !important; float:right; padding: 5px; background-color: #333333; margin: 25px" src="<?= Functions::Image("vDesk", "Me.jpg") ?>" class="Author"
                 alt="Author"><br>
            If you're wondering who's the person behind this project, let me answer this question and introduce myself:<br>
        </p>
        <p>
            Hi, I'm Kerry!<br>
            I am a passionate PHP-, JS-, C#- and SQL-developer living in the beautiful and castle-rich german federal state of "Hessen" and I'm the cause of this huge pile of source
            code to hopefully <span style="text-decoration: line-through;">conquer</span> support the world.
        </p>
        <p>
            If I'm not occupied with developing software and abusing coffee machines, you can usually find me enjoying pizza, bullying my cats or screwing on car engines,
            but after all hobbies I've tried so far, I can clearly tell that programming is my favorite passion of all.
        </p>
        <p>
            Due to earning my first money with small Wordpress projects while being a teenager,
            I've spent the most of my life now as a developer for document management systems, booking and eventplanning solutions as well as shop systems for insurances or clothing.<br>
            So I came to know deep technical abysses in the past which sometimes let me doubt about humanity.
        </p>
        <p>
            So you may wondering why I made a cloud system with a bit of an "enterprise" and "collaborative" touch then?<br>
            Well, I've never thought that I would make the same "mistake", but working on "historically grown" software with unrealistic deadlines
            somehow sparked the need to create something different than those in me, resulting in the project you're currently reading about.
            <br>
            At least I would say, I know how you shouldn't do it, but after all; it still makes a lot of fun developing software and being challenged with customer requirements.
        </p>
        <p>
            PS: I'm hoping this project never won't punish any commercial developers over the work day which have to deal with crappy APIs and packages 😅.
        </p>
    </section>
    <section>
        <h3>How this mess started</h3>
        <p>
            It all started with the need for a simple password protected cloud system where i can just throw in my files and access them from anywhere.<br>
            Unfortunately, every current available solution at this time felt somehow clunky and over-engineered while still not satisfying my rather simple requirements
            or simply hasn't been able to host on my own server.
        </p>
        <p>
            So i began experimenting with JavaScript and PHP and created a small interface to my webserver although I've been working as a .NET-developer at this moment.<br>
            But because I had a constant flow of new ideas for additional features, I kept on developing and improving the system in my freetime until it became, what it is now.
        </p>
        <p>
            While development, vDesk became kind of a "playground" for me, where I can try out new technologies and constantly improve my skills as a developer.<br>
            After many years of figuring out the best way to develop and continuously refactoring,<br>
            I've reached a point, where I thought "hey, maybe someone could use it all" and
            finally took the step to release vDesk to the public domain.
        </p>
        <p>
            vDesk has now reached such a scale that it is no longer easy to work on it alone.<br>
            Therefore I have the hope to lay down the foundation for a great community, creating together an even greater project.<br>
        </p>
    </section>

    <section>
        <h3>How to support</h3>
        <p>
            If you like this project and want to contribute to the development of it,<br>
            you can submit a pull request to the official <a href="https://www.github.com/vDesk-Cloud">Github</a> repository following the rules documented in the
            <a href="<?= Functions::URL("vDesk", "Page", "Contribute") ?>">Contribute</a> and <a href="<?= Functions::URL("Documentation", "Topic", "Development") ?>">Development</a> sections.
        </p>
        <p>
            Otherwise, if you want to help ensuring the server upkeep,
            you can donate a little something via <a target="_blank" href="https://www.paypal.com/paypalme2/developmenthero">PayPal</a> and
            <a target="_blank" href="https://flattr.com/@DevelopmentHero">flattr</a>
            or become a patron on <a target="_blank" href="https://www.patreon.com/DevelopmentHero">Patreon</a>.
        </p>
        <p>
            Many thanks in advance for your support!<br>
            - Kerry
        </p>
    </section>
</article>