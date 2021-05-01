<?php use vDesk\Configuration\Settings; ?>
<article class="Contact">
    <header>
        <h2>Contact</h2>
        <p>
            If you want to provide any feedback or just want to send me a message, you can either send the author an e-mail to <a id="Address">(Enable JavaScript)</a><br>
            or use the contact form below.
        </p>
    </header>
    <script>
        const Address = document.getElementById("Address");
        Address.textContent = [<?= \implode(", ", \array_map(static fn(string $c): int => \ord($c), \str_split (Settings::$Local["Homepage"]["Recipient"]))) ?>].map(c => String.fromCharCode(c)).join("");
        Address.href = `mailto:${Address.textContent}`;
    </script>
    <section id="Contact">
        (Enable JavaScript)
    </section>
    <?php if($Success ?? false): ?>
        <script>
            alert("Your message has been successfully sent!\r\n\r\nThank's for your reply!");
        </script>
    <?php endif; ?>
</article>