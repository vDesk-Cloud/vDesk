<?php use vDesk\Pages\Functions; ?>
<h2>Custom releases</h2>
<p>
    This tutorial describes the creation of custom packages, setups and updates as well as hosting custom updates.<br>
</p>
<h3>Overview</h3>
<ul class="Topics">
    <li><a href="#Packages">Packages</a></li>
    <li>
        <a href="#Setups">Setups</a>
        <ul class="Topics">
            <li><a href="#Exclude">Excluding packages</a></li>
        </ul>
    </li>
    <li>
        <a href="#Updates">Updates</a>
    </li>
</ul>
<hr>
<p>
    As of the modular approach of vDesk, the system is capable of creating custom packages as well as composing entire setups.<br>
    To enable bundling of packages, you'll need to have the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Packages") ?>">Packages</a>, for updates the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Updates") ?>">Updates</a> for setups the <a href="<?= Functions::URL("vDesk", "Page", "Packages#Setup") ?>">Setup</a> and <a
            href="<?= Functions::URL("vDesk", "Page", "Packages#Console") ?>">Console</a>-packages installed (all four are bundled by default in the official full release).<br>
    Press <code class="Inline">° / Shift + ^</code> to open the client side console.
</p>
<h5>Example of creating custom setups</h5>
<section style="text-align: center">
    <aside onclick="this.classList.toggle('Fullscreen')">
        <img style="max-width: 100%; cursor: zoom-in"
                title="The output represents the list of packages that have been bundled into the setup."
                class="CustomSetup"
                src="<?= Functions::Image("Documentation/CustomSetup.png") ?>"
        >
    </aside>
</section>
<hr>
<h3 id="Packages">Packages</h3>
<p>To create a single custom package enter one of the following commands:</p>
<pre><code><span class="Console">Call -M=Packages -C=Create -Package=Archive [-Path=C:\Users, -Compression=<?= \Phar::GZ ?>]
Call -Module=Packages -Command=Create -Package=Calendar [-Path=/home/user, -Compression=<?= \Phar::BZ2 ?>]</span></code></pre>
<p>
    This will create a PHAR archive named like the specified package and bundled with the files and folders of the package at the optionally specified path on the server.<br>
    If you omit the "Path"-parameter, the package file will be created in the systems Server directory.
</p>
<hr>
<h3 id="Setups">Setups</h3>
<p>To create a custom setup enter one of the following commands:</p>
<pre><code><span class="Console">Call -M=Setup -C=Create [-Path=E:\Development\Setups]
Call -Module=Setup -Command=Create [-Path=/var/www/htdocs/vDesk/Server]</span></code></pre>
<p>
    This will create a "Setup.phar"-file bundled with all currently installed packages at the optionally specified path on the server.<br>
    If you omit the "Path"-parameter, the setup file will be created in the systems Server directory.
</p>
<h4 id="Exclude">Excluding packages</h4>
<p>
    If you want to exclude certain packages, you can optionally provide a comma separated list of packages that won't get bundled in the setup.
</p>
<pre><code><span class="Console">Call -M=Setup -C=Create [-Path=%TargetDir%, -Exclude=Pages, Homepage, Documentation, ...]
Call -Module=Setup -Command=Create [-Path=%TargetDir%, -Exclude=%A%, %B%, ...]</span></code></pre>
<p>
    Alternatively, you can provide a JSON array of packages.
</p>
<pre><code><span class="Console">Call -M=Setup -C=Create -Exclude=["Pages", "Homepage", "Documentation", "Contacts", "Messenger"]</span></code></pre>
<hr>
<h3 id="Updates">Updates</h3>
<p>To create a single custom update enter one of the following commands:</p>
<pre><code><span class="Console">Call -M=Updates -C=Create -Update=Archive [-Path=C:\Users, -Compression=<?= \Phar::GZ ?>]
Call -Module=Updates -Command=Create -Update=Calendar [-Path=/home/user, -Compression=<?= \Phar::BZ2 ?>]</span></code></pre>
<p>
    This will create a PHAR archive named like the specified update and bundled with the files and folders of the update and its according package manifest file at the optionally specified path on the server.<br>
    If you omit the "Path"-parameter, the update file will be created in the systems Server directory.
</p>