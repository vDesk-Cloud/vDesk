<?php use vDesk\Pages\Functions; ?>
<section id="Technology" class="SlideIn Right Paused">
    <div>
        <aside id="Platforms" class="Box SlideIn Right Paused" style="animation-delay: 1s; float: right">
            <h2><img src="<?= Functions::Image("vDesk", "Platform.png") ?>"> Platform independent</h2>
            <p>
                v<span style="color: #2AB0ED">D</span>esk is designed to run on any operating system <br>capable of running PHP 8 with minimum permissive settings.
            </p>
        </aside>
        <div style="clear: right"></div>
    </div>
    <div class="Browsers" style="text-align: center">
        <img class="SlideIn Left Paused" style="animation-delay: 0.9s" alt="Microsoft Edge" src="<?= Functions::Image("vDesk", "Platform", "Edge.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 0.5s" alt="Mozilla Firefox" src="<?= Functions::Image("vDesk", "Platform", "Firefox.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 0.7s" alt="Google Chrome" src="<?= Functions::Image("vDesk", "Platform", "Chrome.png") ?>">
    </div>
    <hr>
    <div class="Technologies" style="text-align: center">
        <img class="SlideIn Left Paused" style="animation-delay: 1.1s" alt="PHP 7" src="<?= Functions::Image("vDesk", "Platform", "PHP.png") ?>">
        <img class="SlideIn Left Paused" style="animation-delay: 0.9s" alt="JavaScript" src="<?= Functions::Image("vDesk", "Platform", "JS.png") ?>">
        <img class="SlideIn Left Paused" style="animation-delay: 0.5s" alt="CSS3" src="<?= Functions::Image("vDesk", "Platform", "CSS3.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 0.7s" alt="MySQL" src="<?= Functions::Image("vDesk", "Platform", "MySQL.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 1s" alt="HTML5" src="<?= Functions::Image("vDesk", "Platform", "HTML.png") ?>">
    </div>
    <hr>
    <div class="OperatingSystems" style="text-align: center">
        <img class="SlideIn Left Paused" style="animation-delay: 0.7s" alt="Raspberry Pi" src="<?= Functions::Image("vDesk", "Platform", "RaspberryPi.png") ?>">
        <img class="SlideIn Left Paused" style="animation-delay: 0.5s" alt="Linux" src="<?= Functions::Image("vDesk", "Platform", "Linux.png") ?>">
        <img class="SlideIn Right Paused" style="animation-delay: 0.9s" alt="Windows" src="<?= Functions::Image("vDesk", "Platform", "Windows.png") ?>">
    </div>
    <div style="position: relative">
        <aside id="Resources" class="Box SlideIn Left Paused" style="animation-delay: 1.1s">
            <h2><img src="<?= Functions::Image("vDesk", "Performance.png") ?>"> Small resource footprint</h2>
            <p>
                vDesk is an entirely handcrafted project that comes without any dependencies to third-party libraries or frameworks.
            </p>
            <p>
                Every single line of code is handcrafted, which implies a small footprint of resource usage<br> (It even runs on a Raspberry Pi 2B without any problems).
            </p>
        </aside>
    </div>
</section>
