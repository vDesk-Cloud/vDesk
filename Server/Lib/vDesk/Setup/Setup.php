<?php
if(\PHP_VERSION_ID < 80000) {
    echo "vDesk requires at least PHP version 8.0";
    exit;
}

const Server = __DIR__;
const Client = Server . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Client";

if(\PHP_SAPI === "cli") {
    //Run vDesk in Phar mode.
    include "phar://Setup.phar/Server/Lib/vDesk.php";
    \vDesk::Start(true);
    
    //Run setup.
    \vDesk\Modules::Setup()::Install();
}
