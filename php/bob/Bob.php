<?php

/*
 * By adding type hints and enabling strict type checking, code can become
 * easier to read, self-documenting and reduce the number of potential bugs.
 * By default, type declarations are non-strict, which means they will attempt
 * to change the original type to match the type specified by the
 * type-declaration.
 *
 * In other words, if you pass a string to a function requiring a float,
 * it will attempt to convert the string value to a float.
 *
 * To enable strict mode, a single declare directive must be placed at the top
 * of the file.
 * This means that the strictness of typing is configured on a per-file basis.
 * This directive not only affects the type declarations of parameters, but also
 * a function's return type.
 *
 * For more info review the Concept on strict type checking in the PHP track
 * <link>.
 *
 * To disable strict typing, comment out the directive below.
 */

declare(strict_types=1);

class Bob
{
    public function respondTo(string $statement): string
    {
        $trimmed = trim($statement);

        if ($trimmed === '') {
            return "Fine. Be that way!";
        }

        $isQuestion = substr($trimmed, -1) === '?';
        $hasLetters = preg_match('/[[:alpha:]]/u', $trimmed) === 1;
        $hasLowercase = preg_match('/[[:lower:]]/u', $trimmed) === 1;
        $isYelling = $hasLetters && !$hasLowercase;

        if ($isQuestion && $isYelling) {
            return "Calm down, I know what I'm doing!";
        }

        if ($isQuestion) {
            return "Sure.";
        }

        if ($isYelling) {
            return "Whoa, chill out!";
        }

        return "Whatever.";
    }
}
