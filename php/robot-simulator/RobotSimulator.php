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

class RobotSimulator
{

    public array    $position;
    public string   $direction;
    private array    $cardinalPoint;


    /** @param int[]    $position 
     *  @param string   $direction 
    */
    public function __construct(array $position, string $direction)
    {
        $this->position = $position;
        // $this->direction = $this->position[0] >= 0 ? "north" : "south";
        $this->direction = $direction;
        $this->cardinalPoint = ["north","east","south","west"];
    }

     public function instructions(string $instructions): void
    {
        $instructionsList = str_split(strtoupper($instructions));

        foreach ($instructionsList as $i) {
            $cardinalIndex = array_search($this->direction, $this->cardinalPoint, true);
            if ($cardinalIndex === false) {
                $cardinalIndex = 0;
            }

            switch ($i) {
                case 'L':
                    $cardinalIndex = ($cardinalIndex + 3) % 4;
                    $this->direction = $this->cardinalPoint[$cardinalIndex];
                    break;
                case 'R':
                    $cardinalIndex = ($cardinalIndex + 1) % 4;
                    $this->direction = $this->cardinalPoint[$cardinalIndex];
                    break;
                case 'A':
                    switch ($this->direction) {
                        case 'north': $this->position[1] ++ ; break; 
                        case 'south': $this->position[1] -- ; break; 
                        case 'east':  $this->position[0] ++ ; break; 
                        case 'west':  $this->position[0] -- ; break; 
                    }
                    break;
                default:
                    break;
            }
        }
    }

    public function getPosition(): array
    {
       return $this->position;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}




