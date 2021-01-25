"use strict";
/**
 * Creates a new AboutDialog.
 * @class
 * @memberOf vDesk
 * @extends vDesk.Controls.Window
 */
vDesk.AboutDialog = function AboutDialog() {
    this.Extends(vDesk.Controls.Window, vDesk.Visual.Icons.Unknown, `${vDesk.Locale.vDesk.About} vDesk`, null, true, 500, 700);

    /**
     * The about GroupBox of the AboutDialog.
     * @type {vDesk.Controls.GroupBox}
     */
    const About = new vDesk.Controls.GroupBox(
        "About",
        [vDesk.AboutDialog.Description]
    );
    About.Control.classList.add("About");
    this.Content.appendChild(About.Control);

    /**
     * The license GroupBox of the AboutDialog.
     * @type {vDesk.Controls.GroupBox}
     */
    const License = new vDesk.Controls.GroupBox(
        "License",
        [vDesk.AboutDialog.License],
        true,
        true
    );
    License.Control.classList.add("License");
    this.Content.appendChild(License.Control);
};

/**
 * The description of the AboutDialog.
 * @const
 * @type {HTMLElement}
 */
vDesk.AboutDialog.Description = document.createElement("article");
vDesk.AboutDialog.Description.innerHTML = `<p>v<span style="color: var(--Foreground)">D</span>esk - JavaScript, PHP and SQL supported cloud-platform.</p>
<p>
    If you want to know more about this software, visit the official project website at <a target="_blank" href="https://vdesk.cloud">https://vdesk.cloud</a>.
</p>
<p>
    If you have any questions, require support or provide feedback, you can contact the author by sending an email to <a href='mailto:DevelopmentHero@gmail.com'>DevelopmentHero@gmail.com</a> or using the contact form at at <a target="_blank" href="https://vdesk.cloud/Contact">https://vdesk.cloud/Contact</a>.
</p>
<p>
    If you want to contribute to this software, visit the contribution guideline at <a target="_blank" href="https://vdesk.cloud/Contribute">https://vdesk.cloud/Contribute</a> or the official repository on <a href="https://www.github.com/vDesk-Cloud">Github</a>.
</p>
<p>
    If you like this software and want to support its development by donating something to its author, you can read more about possible ways at <a target="_blank" href="https://vdesk.cloud/Donate">https://vdesk.cloud/Donate</a>
</p>
<p>
    This software uses icons from <a target="_blank" href="https://www.icons8.com/">https://www.icons8.com/</a>.
</p>`;

/**
 * The license of the AboutDialog.
 * @const
 * @type {HTMLElement}
 */
vDesk.AboutDialog.License = document.createElement("article");
vDesk.AboutDialog.License.innerHTML = `<h1>Microsoft Public License (Ms-PL)</h1>
<p>
    This license governs use of the accompanying software. If you use the
    software, you accept this license. If you do not accept the license,
    do not use the software.
</p>
<h2>1. Definitions</h2>
<p>
    The terms "reproduce," "reproduction," "derivative works," and
    "distribution" have the same meaning here as under U.S. copyright
    law.
</p>    
<p>
    A "contribution" is the original software, or any additions or
    changes to the software.
</p>    
<p>
    A "contributor" is any person that distributes its contribution
    under this license.
</p>    
<p>
    "Licensed patents" are a contributor's patent claims that read
    directly on its contribution.
</p>    
<h2>2. Grant of Rights</h2>
<p>
    (A) Copyright Grant- Subject to the terms of this license,
    including the license conditions and limitations in section 3,
    each contributor grants you a non-exclusive, worldwide,
    royalty-free copyright license to reproduce its contribution,
    prepare derivative works of its contribution, and distribute its
    contribution or any derivative works that you create.
</p>    
<p>   
    (B) Patent Grant- Subject to the terms of this license, including
    the license conditions and limitations in section 3, each
    contributor grants you a non-exclusive, worldwide, royalty-free
    license under its licensed patents to make, have made, use, sell,
    offer for sale, import, and/or otherwise dispose of its
    contribution in the software or derivative works of the
    contribution in the software.
</p>    
<h2>3. Conditions and Limitations</h2>
<p>  
    (A) No Trademark License- This license does not grant you rights
    to use any contributors' name, logo, or trademarks.
</p>    
<p>
    (B) If you bring a patent claim against any contributor over
    patents that you claim are infringed by the software, your patent
    license from such contributor to the software ends automatically.
</p>    
<p>
    (C) If you distribute any portion of the software, you must retain
    all copyright, patent, trademark, and attribution notices that are
    present in the software.
</p>    
<p>
    (D) If you distribute any portion of the software in source code
    form, you may do so only under this license by including a
    complete copy of this license with your distribution. If you
    distribute any portion of the software in compiled or object code
    form, you may only do so under a license that complies with this
    license.  
</p>    
<p>
    (E) The software is licensed "as-is." You bear the risk of using
    it. The contributors give no express warranties, guarantees, or
    conditions. You may have additional consumer rights under your
    local laws which this license cannot change. To the extent
    permitted under your local laws, the contributors exclude the
    implied warranties of merchantability, fitness for a particular
    purpose and non-infringement.
</p>`;