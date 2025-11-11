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

class FlowerField
{
    public array $garden;
    public array $map;
    public int $rows;
    public int $cols;
    public array $result;

    public function __construct(array $garden)
    {
        $this->garden = $garden;
        $this->rows = !empty($this->garden) ? count($this->garden) : 0;
        $this->cols = !empty($this->garden) ? strlen($this->garden[0]) : 0;
        $this->getCoordinate();
    }

    public function getCoordinate()
    {
        if (empty($this->garden)) {
            $this->map = [];
        } elseif ($this->garden == [""]) {
            $this->map = [""];
        } else {
            // Loop on each chars to determinate the coordinate
            $y = 0;
            foreach ($this->garden as $case) {
                str_split($case);
                $x = 0;
                while ($x < $this->cols) {
                    $this->map[] =  [$x, $y, $case[$x]];
                    $x++;
                }
                $y++;
            }
        }
    }

    public function getMinesNumber(array $position): int | string
    {
        $count = 0;

        $startX = $position[0] - 1;
        $endX   = $position[0] + 1;
        $startY = $position[1] - 1;
        $endY   = $position[1] + 1;

        for ($x = $startX; $x <= $endX; $x++) {
            for ($y = $startY; $y <= $endY; $y++) {

                if ($x < 0 || $y < 0 || $x >= $this->cols || $y >= $this->rows) {
                    continue;
                }
                // skip the center cell itse
                if ($x === $position[0] && $y === $position[1]) {
                    continue;
                }
                if (in_array([$x, $y, '*'], $this->map, true)) {
                    $count++;
                }
            }
        }
        return $count == 0 ? " " : $count;
    }

    public function annotate(): array
    {
        if (empty($this->garden)) {
            return [];
        } elseif ($this->garden == [""]) {
            return [""];
        } else {
            foreach ($this->map as $case) {
                if ($case[2] == " ") {
                    $res[] =  $this->getMinesNumber($case);
                } else {
                    $res[] = $case[2];
                }
            }
            $res = array_chunk($res, $this->cols);
            $result = [];
            foreach($res as $row){
                $result[] = implode('',$row);
            }
            return  $result;
        }
    }
}

$garden = [
    " * ",
    "   ",
    "  *",
];

$test = new FlowerField($garden);
print "<pre>";
// print_r($test->garden);
// print_r($test->map);
print_r($test->annotate());