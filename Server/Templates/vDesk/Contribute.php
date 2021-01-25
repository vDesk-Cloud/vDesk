<?php use vDesk\Pages\Functions; ?>
<section>
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
    <hr>
    <h3>Code of conduct</h3>
    <p>
        Do we really have to discuss about that?<br>
        We're all humans, regardless of origin, skin colour or religion which have to share one and the same planet!<br>
        So, i appeal to respect each other and stay polite.
    </p>
    <hr>
    <h3>Setting up a development environment</h3>
    <p>
        Install the system as described in the <a href="<?= Functions::URL("vDesk", "Page", "GetvDesk") ?>">GetvDesk</a> section.<br>
        Depending on your type of contribution, you may choose between the standard release or the development bundle, which contains the current content of the <a href="https://www.github.com/vDesk-Cloud/vDesk/">Github</a> repository.<br>
        After the installation has been completed, open a console terminal, <code class="Inline"><span class="Console">cd</span></code> to the installation directory and initialize the repository by entering the following commands:
    </p>
    <pre><code><span class="Console">git init
git add --all
git remote add origin https://github.com/vDesk-Cloud/vDesk.git
</span></code></pre>
    <p>Checkout the ignore file to prevent unwanted files from being committed:</p>
    <pre><code><span class="Console">git fetch origin master
git checkout origin/master -- .gitignore
</span></code></pre>
    <hr>
    <h3>Contribution of source code</h3>
    <p>
        Before opening a pull request against the Development-branch, consider reading the documentation about <a
                href="<?= Functions::URL("Documentation", "Page", "Development") ?>">Development</a> and <a href="<?= Functions::URL("Documentation", "Page", "Packages") ?>">Package</a>
        specifications.
    </p>
    <h4>Branches</h4>
    <p>
        Branchnames must consist of the target package's name, new version number and topic.<br>
        For example, a bugg-fix for the "Archive"-package would result in a branch-name like <code class="Inline">Archive-1.0.1-FixBugg</code>.
    </p>
    <p>
        If your contribution addresses a bugg-fix, you can directly commit to the Development-branch and open a pull request.
    </p>
    <p>
        If your contribution provides new functionality or deprecates existing functions, create a feature-branch which name references the target package and feature to
        add/drop.<br>
        For example: If you want to provide
        <code class="Inline">Archive.VersionControl</code>, name your branch like <code class="Inline">Archive-1.1.0-VersionControl</code>.<br>
        Address the type of change in a proper commit message.
    </p>
    <p>
        If your contribution implements an entirely new package, create a separate branch following the previous naming rules.
    </p>
    <h4>Translations</h4>
    <p>
        If you want to contribute a languagepack, consider reading the <a href="<?= Functions::URL("Documentation", "Page", "Packages#CustomPackages") ?>">Packages, Updates and
            Setups</a>-documentation for an example implementation of a translation package.<br>
        Language-packs must reference the target package followed by the locale the language-pack addresses separated by a dot.<br>
        For example, if you want to contribute spain translations for the "Archive"-package, name your package <code class="Inline">Archive.ES</code>.
    </p>
</section>