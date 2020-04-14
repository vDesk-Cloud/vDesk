<?php
if(\PHP_VERSION_ID < 70400){
    echo "vDesk requires at least PHP version 7.4.0";
    exit;
}

const Server = __DIR__;
const Client = Server . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Client";

if(\PHP_SAPI === "cli") {
    //Run vDesk in Phar mode.
    include "phar://Setup.phar/Server/Lib/vDesk.php";
    \vDesk::Start(true);

    //Run setup.
    \vDesk\Modules::Packages()::InstallSetup();
}
