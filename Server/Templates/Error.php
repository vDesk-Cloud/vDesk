<?php
declare(strict_types=1);

use vDesk\Pages\Response;

/** @var Throwable $Exception */
/**
 * @param array $Parameters
 *
 * @return string
 */
$PrintParameters = static function(array $Parameters): string {
    return \implode(
        "<b>,</b> ",
        \array_map(
            static function($Value): string {
                $String = \json_encode($Value);
                return "<span class=\"argument " . \gettype($Value) . "\" title='{$String}'>" . (\strlen($String) > 30 ? \substr($String, 0, 30) . "..." : $String) . "</span>";
            },
            $Parameters
        )
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
    <link rel="icon" href="http://localhost/vDesk-dev/Server/Images/error.ico" type="image/x-icon">
    <style>
        html {
            font-family: Arial, serif;
        }

        .stacktrace {
            border-radius: 5px;
            border: 1px solid #999;
            text-align: left;
            padding: 20px 20px 10px 40px;
            margin: 0;
            font-family: Courier New, monospace;
            background-color: #333;
            color: wheat;
        }

        .tracepart {
            border-bottom: 1px solid #666;
            margin-bottom: 10px;
        }

        .file {
            color: gray;
        }

        .line {
            color: firebrick;
        }

        .class {
            color: goldenrod;
        }

        .type {
            color: wheat;
        }

        .function {
            color: gold;
        }

        .argument {
        }

        .argument.boolean, .argument.NULL {
            color: orange;
        }

        .argument.double, .argument.integer {
            color: lightblue;
        }

        .argument.string {
            color: darkolivegreen;
        }

        .argument.object {
            color: Green;
        }

        .argument.array {
            color: Olive;
        }

        .argument.resource {
            color: Orchid;
        }
    </style>
</head>
<body>
<article class="error"
         style="width: 60%; margin-left: 20%; text-align: center;">
    <header style="text-align: center;">
        <h1><?= Response::$Code ?> - <?= $Exception->getMessage() ?></h1>
        <h3> Whoops! The server encountered an error!</h3>
    </header>
    <section class="content" style="text-align: center;">
        <h3>The computer says:</h3>
        <p>[<?= $Exception->getCode() ?>] <?= $Exception->getMessage() ?></p>
        <p><?= \get_class($Exception) ?><br>In file "<span class="file"><?= $Exception->getFile() ?></span>"
            at line <span class="line"><?= $Exception->getLine() ?></span>
        </p>
        <h4>Stacktrace:</h4>
        <ol class="stacktrace">
            <?php foreach($Exception->getTrace() as $Part): ?>
                <li class="tracepart">
                    <span class="class"><?= \trim($Part["class"] ?? "") ?></span><span class="type"><?= \trim($Part["type"] ?? "") ?></span><span
                            class="function"><?= \trim($Part["function"] ?? "") ?></span>(<?= $PrintParameters($Part["args"] ?? []) ?>)
                    <br>
                    <?php if(isset($Part["file"])): ?>
                        In file "<span class="file"><?= $Part["file"] ?></span>"
                    <?php endif; ?>
                    <?php if(isset($Part["line"])): ?>
                        at line <span class="line"><?= $Part["line"] ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
</article>
</body>
</html>