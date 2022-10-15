<?php use vDesk\Pages\Functions; ?>
<section id="Customization" class="SlideIn">
    <img src="<?= Functions::Image("vDesk", "Packages.png") ?>">
    <div class="PackageSystem SlideIn Right Box Paused" style="animation-delay: 0.5s">
        <h2>Customizable <img src="<?= Functions::Image("vDesk", "Customizable.png") ?>"></h2>
        <p>
            v<span style="color: #2AB0ED">D</span>esk ships with a powerful yet simple package system that allows running installations to be customized on the fly.<br>
            It's entirely your decision which features the system provides!<br>
            (Even this website is a <a href="<?= Functions::URL("vDesk", "Page", "Packages#Homepage") ?>">package</a> that is based on another <a
                href="<?= Functions::URL("vDesk", "Page", "Packages#Pages") ?>">package</a> that implements a simple MVC-framework.)
        </p>
    </div>
    <div class="Packages SlideIn Right Box Paused" style="animation-delay: 0.75s">
        <h2><img src="<?= Functions::Image("vDesk", "Package.png") ?>"> Packages</h2>
        <p>The standard release of v<span style="color: #2AB0ED">D</span>esk contains a preselected collection of feature rich packages</p>
        <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages") ?>">Explore packages</a>
    </div>
    <div class="OpenSource SlideIn Right Box Paused" style="animation-delay: 1s">
        <h2><img src="<?= Functions::Image("vDesk", "Code.png") ?>"> Open source</h2>
        <p>
            v<span style="color: #2AB0ED">D</span>esk is licensed under the Microsoft Public License which allows package-authors to create custom setups bundled with their own
            licensed packages.<br>
        </p>
        <a class="Button" href="<?= Functions::URL("Documentation", "Topic", "Packages") ?>">Learn more about custom packages</a>
    </div>
</section>
