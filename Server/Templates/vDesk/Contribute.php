<?php
use vDesk\Pages\Functions;
use vDesk\Documentation\Code;
?>
<article class="Contribute">
    <header>
        <h2>Contribute</h2>
        <p>
            Greetings, fellow developer!<br><br>

            You have a great idea,
            You fixed a bugg, made a cool feature or spell-casted an entire package?<br>
            Then you visited the correct page!
        </p>
        <p>
            As great as the idea of open source software and the spirit of their communities is,<br>
            unfortunately, there have to be a few rules to prevent the project from drowning in total chaos.
        </p>
    </header>
    <section>
        <h3>Code of conduct</h3>
        <p>
            Do we really have to discuss about that?<br>
            We're all humans, regardless of origin, skin colour or religion which have to share one and the same planet!<br>
            So, i appeal to respect each other and stay polite.
        </p>
    </section>
    <section>
        <h3>Setting up a development environment</h3>
        <p>
            Install the system as described in the <a href="<?= Functions::URL("vDesk", "Page", "GetvDesk") ?>">GetvDesk</a> section.<br>
            Depending on your type of contribution, you may choose between the standard release or the development bundle, which contains the current content of the <a
                    href="https://www.github.com/vDesk-Cloud/vDesk/">Github</a> repository.<br>
            After the installation has been completed, open a console terminal, <code class="Inline"><?= Code::Console("cd") ?></code> to the installation directory and initialize the repository
            by entering the following commands:
        </p>
        <pre><code><?= Code::Console("git init
git add --all
git remote add origin https://github.com/vDesk-Cloud/vDesk.git") ?></code></pre>
        <p>Checkout the ignore file to prevent unwanted files from being committed:</p>
        <pre><code><?= Code::Console("git fetch origin master
git checkout origin/master -- .gitignore") ?></code></pre>
    </section>
    <section>
        <h3>Contribution of source code</h3>
        <p>
            Before opening a pull request against the Development-branch, consider reading the documentation about
            <a href="<?= Functions::URL("Documentation", "Topic", "Development") ?>">Development</a> and
            <a href="<?= Functions::URL("Documentation", "Topic", "Packages") ?>">Package</a> specifications.
        </p>
        <h4>Branches</h4>
        <p>
            Branches must only consist of commits addressing buggs or features of a single package (unless it's a bundle of multiple packages).<br>
            Branch names must consist of the target package's name and targeted new version number.<br>
            For example, a bugg-fix for the "Archive"-package in version 1.0.0 would result in a branch-name like <code class="Inline">Archive-1.0.1</code>.<br>

            Visit the <a href="<?= Functions::URL("Documentation", "Topic", "Packages#Versioning") ?>">Versioning</a>-section of the <a
                    href="<?= Functions::URL("Documentation", "Topic", "Packages") ?>">Packages, Updates and
                Setups</a>-documentation for further information about versioning.
        </p>
        <h4>Pull requests</h4>
        <p>
            Pull requests shall describe whether its commits address buggfixes or implements new-/deprecates old functionality and have to be opened against the master branch.<br>
            After any commits have been successfully merged, a new release for the package, update, setup and development bundle will be created.
        </p>
        <h4>Translations</h4>
        <p>
            If you want to contribute a language-pack, consider reading the <a href="<?= Functions::URL("Documentation", "Topic", "Packages#CustomPackages") ?>">Packages, Updates and
                Setups</a>-documentation for an example implementation of a translation package.<br>
            Language-packs must reference the target package followed by the locale the language-pack addresses separated by a dot.<br>
            For example, if you want to contribute spain translations for the "Archive"-package, name your package <code class="Inline">Archive.ES</code>.
        </p>
    </section>
</article>