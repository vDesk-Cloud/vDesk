<?php
declare(strict_types=1);
const Server = __DIR__;
const Client = Server . \DIRECTORY_SEPARATOR . ".." . \DIRECTORY_SEPARATOR . "Client";
include Server . \DIRECTORY_SEPARATOR . "Lib" . \DIRECTORY_SEPARATOR . "vDesk.php";
vDesk::Start();
vDesk::Run();