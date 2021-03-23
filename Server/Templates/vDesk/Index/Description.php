<?php
use vDesk\Pages\Functions;
/** @var \Pages\vDesk\Index $Page */
?>
<section id="Description" class="SlideIn Bottom">
    <div class="Previews">
        <h2>v<span style="color: #2AB0ED">D</span>esk</h2>
        <img class="SlideIn Left Archive" src="<?= Functions::Image("Packages", "ArchiveOverview.png") ?>" alt="Archive">
        <img class="SlideIn Top Calendar" src="<?= Functions::Image("Packages", "CalendarMonthView.png") ?>" alt="Calendar">
        <img class="SlideIn Right Colors" src="<?= Functions::Image("Packages", "Colors.png") ?>" alt="Colors">
        <img class="SlideIn Bottom Configuration" src="<?= Functions::Image("Packages", "Configuration.png") ?>" alt="Configuration">
        <img class="SlideIn Left Contacts" src="<?= Functions::Image("Packages", "Contacts.png") ?>" alt="Contacts">
        <img class="SlideIn Right Pinboard" src="<?= Functions::Image("Packages", "Pinboard.png") ?>" alt="Pinboard">
        <img class="SlideIn Left Search" src="<?= Functions::Image("Packages", "Search.png") ?>" alt="Search">
        <img class="SlideIn Left Messenger" src="<?= Functions::Image("Packages", "Messenger.png") ?>" alt="Messenger">
        <img class="SlideIn Bottom Machines" src="<?= Functions::Image("Packages", "Machines.png") ?>" alt="Machines">
        <img class="SlideIn Top Updates" src="<?= Functions::Image("Packages", "Updates.png") ?>" alt="Updates">
        <img class="SlideIn Left Console" src="<?= Functions::Image("Packages", "Console.png") ?>" alt="Console">
    </div>
    <div class="Box SlideIn Left">
        <p>
            v<span style="color: #2AB0ED">D</span>esk is a self hosted open source personal cloud<br>
            that has been built with the focus of combining a simple yet intuitive client<br>
            with a feature-rich and customizable server environment.
        </p>
            <a class="Button" href="<?= Functions::URL("vDesk", "Page", "GetvDesk") ?>">Get vDesk</a>
    </div>
    <div class="Box SlideIn Right">
        <p>
            Access and manage your files from anywhere, organize your dates and stay in contact with others. <br>
            v<span style="color: #2AB0ED">D</span>esk comes with a predefined feature rich set of packages.
        </p>
            <a class="Button" href="#Features">🡳 Checkout features 🡳</a>
    </div>
</section>