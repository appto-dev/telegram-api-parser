<?php

namespace TelegramApiParser\CodeGenerator\Framework\Support;

use Nette\PhpGenerator\ClassLike;
use Nette\PhpGenerator\GlobalFunction;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;

class MyPrinter extends Printer
{
    public string $indentation = '    ';
    public int $linesBetweenMethods = 1;
    public int $linesBetweenUseTypes = 1;
    public int $wrapLength = 100;
    private const PLACEHOLDER = "\x00";

    protected function isBraceOnNextLine(bool $multiLine, bool $hasReturnType): bool {
        return !$multiLine;
    }

    protected function printDocComment($commentable): string {
        $multiLine = $commentable instanceof GlobalFunction
            || $commentable instanceof Method
            || $commentable instanceof ClassLike
            || $commentable instanceof PhpFile;

        $comment = $commentable->getComment();
        $length = $commentable instanceof ClassLike && str_contains($comment ?? '', '- <a')
            ? max($this->wrapLength, 125)
            : $this->wrapLength;

        $text = $comment ? $this->wrap($comment, $length) : '';

        return Helpers::formatDocComment($text, $multiLine);
    }

    private function wrap(string $text, int $length = 80): string {
        // <a href="...">object</a>, <code>...</code>, <em>...</em> и т.п. - целиком одно "слово"
        $protected = preg_replace_callback(
            '/<(\w+)([^>]*)>.*?<\/\1>/is',
            static fn(array $m) => str_replace(' ', self::PLACEHOLDER, $m[0]),
            $text
        );

        $wrapped = wordwrap($protected, $length, "\n", false);
        $wrapped = str_replace(self::PLACEHOLDER, ' ', $wrapped);

        $lines = explode("\n", $wrapped);

        return implode("\n", array_map(static fn(string $line) => $line, $lines));
    }
}
