<?php
declare(strict_types=1);

/**
 * The path of the folder within the current client entry point is running.
 */
const Server = __DIR__;
const Client = Server . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Client";

include Server . DIRECTORY_SEPARATOR . "Lib" . DIRECTORY_SEPARATOR . "vDesk.php";
include Server . DIRECTORY_SEPARATOR . "Lib" . DIRECTORY_SEPARATOR . "Pages.php";

Pages::Start();
Pages::Run();