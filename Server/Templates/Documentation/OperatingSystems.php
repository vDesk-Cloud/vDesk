<?php use vDesk\Pages\Functions; ?>
<h2>Operating systems</h2>
<p>
    This is an enumeration of verified operating systems on which vDesk runs.<br>
    If you want to report about successful installations on a system not listed below, you can send the author a short <a
            href="<?= Functions::URL("vDesk", "Page", "Contact", "Topic", "OS", "Message", "It run's on:") ?>">message</a>.
</p>
<h3>NT-based systems</h3>
<ul>
    <li>Windows 10 Home, x64 (using <a target="_blank" href="https://www.apachefriends.org">XAMPP</a> with default config)</li>
    <li>Windows 10 Professional, x64</li>
</ul>
<h3>*nix-based systems</h3>
<ul>
    <li>Raspbian (Buster), Kernel 4.19 (on Raspberry Pi 2&3 B)</li>
</ul>