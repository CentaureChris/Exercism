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

class GradeSchool
{
    /** @var array<int, array<string>> */
    public array $sortedRoster = [];

    /** @var array<string> */
    public $roster = [];

    public function add(string $name, int $grade): bool
    {
        if(in_array($name,$this->roster)){
            return false;
        }else{ 
            $this->sortedRoster[$grade][] = $name;
            sort($this->sortedRoster[$grade]);
            ksort($this->sortedRoster);
            $this->roster = array_merge(...array_values($this->sortedRoster));
            return true;
        }
    }

    public function grade(int $grade): array
    {
        return $this->sortedRoster[$grade]?$this->sortedRoster[$grade]:[];
    }

    public function roster(): array
    {
        
        return $this->roster;
    }
}

$test = new GradeSchool();

$test->add( 'Lisa', 2);
$test->add( 'Jean', 1);
$test->add( 'Pierre', 1);
$test->add( 'Alain', 1);