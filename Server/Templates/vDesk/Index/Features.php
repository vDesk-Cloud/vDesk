<?php use vDesk\Pages\Functions; ?>
<section id="Features" class="SlideIn">
    <div id="FeatureSlideShow" class="SlideShow">
        <div class="Slide Preview" id="Archive">
            <h2>Archive</h2>
            <aside class="Box" id="FileShare">
                <h4>Access your files from anywhere</h4>
                <p>AccessControlList-based sharing of files and folders with users and groups</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Archive") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/ArchiveOverview.png") ?>" alt="Archive">
        </div>
        <div class="Slide Preview" id="Calendar">
            <h2>Calendar</h2>
            <aside class="Box">
                <h4>Keep track of your business</h4>
                <p>AccessControlList-based event planning with users and groups</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Calendar") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/CalendarMonthView.png") ?>" alt="Calendar">
        </div>
        <div class="Slide Preview" id="Contacts">
            <h2>Contacts</h2>
            <aside class="Box">
                <h4>Stay in contact with friends and business partners</h4>
                <p>AccessControlList-based contact-management of private- and company contacts</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Contacts") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Contacts.png") ?>" alt="Contacts">
        </div>
        <div class="Slide Preview" id="Messenger">
            <h2>Messenger</h2>
            <aside class="Box">
                <h4>Exchange with others</h4>
                <p>Have private conversations or discuss in groups</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Messenger") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Messenger.png") ?>" alt="Messenger">
        </div>
        <div class="Slide Preview" id="Pinboard">
            <h2>Pinboard</h2>
            <aside class="Box">
                <h4>Organize yourself</h4>
                <p>Create custom notes and attach frequently used files and folders</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Pinboard") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Pinboard.png") ?>" alt="Pinboard">
        </div>
        <div class="Slide Preview" id="Search">
            <h2>Search</h2>
            <aside class="Box">
                <h4>Keep the overview</h4>
                <p>Quickly find files, folders, calendar-events or contacts</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Search") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Search.png") ?>" alt="Search">
        </div>
        <div class="Slide Preview" id="Colors">
            <h2>Colors</h2>
            <aside class="Box">
                <h4>Life is colorful</h4>
                <p>Customize the <i>look&feel</i> of v<span style="color: #2AB0ED">D</span>esk like your taste</p>
                <a class="Button" href="<?= Functions::URL("vDesk", "Page", "Packages#Colors") ?>">Learn more</a>
            </aside>
            <img src="<?= Functions::Image("Packages/Colors.png") ?>" alt="Colors">
        </div>
    </div>
</section>
