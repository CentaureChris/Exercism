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

class Tournament
{

    public string $competitionResume;

    public function __construct() {}

    public function tally($scoresResume): string
    {
        if($scoresResume == ""){
            return "Team                           | MP |  W |  D |  L |  P";
        }
        $teamHeaderWithSpaces = "Team                           ";
        $header = $teamHeaderWithSpaces . "| MP |  W |  D |  L |  P";
        $scores = $header;
        $scoresResume = $this->getStats($scoresResume);
        uksort($scoresResume, function ($teamA, $teamB) use ($scoresResume) {
            $pa = $scoresResume[$teamA]['P'];
            $pb = $scoresResume[$teamB]['P'];

            // 1) By points, descending
            if ($pa !== $pb) {
                return $pb <=> $pa;
            }

            // 2) Tie-break by team name, ascending (alphabetical)
            return strcmp($teamA, $teamB);
        });

        foreach ($scoresResume as $team => $stats) {
            $new = $team . substr($teamHeaderWithSpaces, strlen($team));
            $new = substr($new, 0, strlen($teamHeaderWithSpaces) - 1);
            $scores .= "\n" . $new . " |  " . ($stats["W"] + $stats["D"] + $stats["L"]) . " |  " . $stats["W"] . " |  " . $stats["D"] . " |  " . $stats["L"] . " |  " . $stats["P"];
        }
        return $scores;
    }

    public function formatFile($resume): array
    {
        $matches = explode("\n", trim($resume));
        return $matches;
    }

    public function getResult(string $resultLine): array|int
    {
        $result = explode(";", $resultLine);
        $outcome = $result[2];

        switch ($outcome) {
            case 'win':
                return [3, 0];
            case 'loss':
                return [0, 3];
            case 'draw':
                return [1, 1];
            default:
                return 0;
        }
    }

    public function getStats($resume)
    {
        $matches = $this->formatFile($resume);
        $stats = [];

        foreach ($matches as $match) {
            $match = explode(";", $match);
            $team1 = $match[0];
            $team2 = $match[1];

            if (!array_key_exists($team1, $stats)) {
                $stats[$team1] = ["W" => 0, "D" => 0, "L" => 0, "P" => 0];
            }
            switch ($match[2]) {
                case 'win':
                    $stats[$team1]["W"] += 1;
                    $stats[$team1]["P"] += 3;
                    break;
                case 'loss':
                    $stats[$team1]["L"] += 1;
                    break;
                case 'draw':
                    $stats[$team1]["D"] += 1;
                    $stats[$team1]["P"] += 1;
                    break;
            }


            if (!array_key_exists($team2, $stats)) {
                $stats[$team2] = ["W" => 0, "D" => 0, "L" => 0, "P" => 0];
            }
            switch ($match[2]) {
                case 'win':
                    $stats[$team2]["L"] += 1;
                    break;
                case 'loss':
                    $stats[$team2]["W"] += 1;
                    $stats[$team2]["P"] += 3;
                    break;
                case 'draw':
                    $stats[$team2]["D"] += 1;
                    $stats[$team2]["P"] += 1;
                    break;
            }
        }
        return $stats;
    }
}

$resume = "Courageous Californians;Devastating Donkeys;win\n" .
    "Allegoric Alaskans;Blithering Badgers;win\n" .
    "Devastating Donkeys;Allegoric Alaskans;loss\n" .
    "Courageous Californians;Blithering Badgers;win\n" .
    "Blithering Badgers;Devastating Donkeys;draw\n" .
    "Allegoric Alaskans;Courageous Californians;draw";

$tournament = new Tournament();

print '<pre>';
print $tournament->tally($resume);