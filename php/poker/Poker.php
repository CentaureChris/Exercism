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

class Poker
{

    public array $bestHands = [];

    public array $cardValue = [
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        'J' => 11,
        'Q' => 12,
        'K' => 13,
        'A' => 14,
    ];

    public array $handRank = [
        "ROYAL FLUSH",
        "STRAIGHT FLUSH",
        "FOUR OF KIND",
        "FULL HOUSE",
        "FLUSH",
        "STRAIGHT",
        "THREE OF KIND",
        "TWO PAIR",
        "PAIR",
        "HIGH CARD"
    ];

    public function __construct(array $hands)
    {
        $handsSorted = [];
        foreach ($hands as $hand) {
            $handDetails = [
                "hand" => $hand,
                "value" => $this->evaluateHand($hand),
                "score" => 0
            ];
            $handDetails['score'] = $this->getRank($handDetails['value']);
            array_push($handsSorted, $handDetails);
        }

        $handsToCompare = [];
        foreach ($handsSorted as $hand) {
            $hand = $this->splitHandTocard($hand['hand']);
            usort($hand, function ($a, $b) {
                return (int)$this->cardValue[$b['value']] <=> (int)$this->cardValue[$a['value']];
            });
            array_push($handsToCompare, array_reverse($hand));
        }

        $sortedHands = $this->compareHands($handsToCompare);
        $sortedHands = $this->handsArrayToStrings($sortedHands);
        var_dump($sortedHands);
        exit;
    }

    public function splitHandTocard(string $hand)
    {
        $cardList = [];
        $cards = explode(",", $hand);

        foreach ($cards as $card) {
            $card = trim($card);
            if ($card === '') {
                continue;
            }
            $c = [];
            if (strlen($card) == 3) {
                $c["value"] = $card[0] . $card[1];
                $c["color"] = $card[2];
            } elseif (strlen($card) == 2) {
                $c["value"] = $card[0];
                $c["color"] = $card[1];
            } else {
                continue;
            }
            array_push($cardList, $c);
        }
        return $cardList;
    }

    public function handsArrayToStrings(array $hands): array
    {
        return array_map(function ($hand) {
            return implode(',', array_map(
                fn($card) => $card['value'] . $card['color'],
                $hand
            ));
        }, $hands);
    }

    public function evaluateHand(string $hand): string | int
    {
        $hand = $this->splitHandTocard($hand);

        $order = $this->cardValue;
        usort($hand, fn($a, $b) => $order[$a['value']] <=> $order[$b['value']]);

        // count occurrences of each value (e.g. ['2'=>2,'4'=>2,'J'=>4])
        $values = array_column($hand, 'value');
        $counts = array_count_values($values);

        // keep only duplicates counts (e.g. [2,2,4])
        $dupeCounts = array_values(array_filter($counts, fn($c) => $c > 1));

        // count the counts (e.g. [2=>2,4=>1] meaning "two pairs" and "one four-kind")
        $somes = array_count_values($dupeCounts);

        $pairs  = $somes[2] ?? 0;
        $trips  = $somes[3] ?? 0;
        $quads  = $somes[4] ?? 0;

        $handValue = "";
        switch (true) {
            case $quads === 1:
                $handValue = "FOUR OF KIND";
                break;
            case $trips === 1 && $pairs === 1:
                $handValue = "FULL HOUSE";
                break;
            case $trips === 1:
                $handValue = "THREE OF KIND";
                break;
            case $pairs === 2:
                $handValue = "TWO PAIR";
                break;
            case $pairs === 1:
                $handValue = "PAIR";
                break;
            default:
                $handValue = "";
        }
        if ($handValue != "") {
            return $handValue;
        }

        $values = array_map(fn($card) => $order[$card['value']], $hand);
        sort($values);
        $uniqueValues = array_values(array_unique($values));
        $isFlush = count(array_unique(array_column($hand, 'color'))) === 1;
        $isStraight = false;

        if (count($uniqueValues) === 5) {
            $isStraight = true;
            for ($i = 1; $i < 5; $i++) {
                if ($uniqueValues[$i] !== $uniqueValues[$i - 1] + 1) {
                    $isStraight = false;
                    break;
                }
            }

            if (!$isStraight && $uniqueValues === [2, 3, 4, 5, 14]) {
                $isStraight = true;
            }
        }

        if ($isFlush && $isStraight) {
            if ($uniqueValues === [10, 11, 12, 13, 14]) {
                return "ROYAL FLUSH";
            }
            return "STRAIGHT FLUSH";
        } elseif ($isFlush) {
            return "FLUSH";
        } elseif ($isStraight) {
            return "STRAIGHT";
        }

        $score = $this->cardValue[$hand[4]['value']];

        return $score;
    }

    public function getRank(string|int $hand): string | int
    {
        if (gettype($hand) == "integer") {
            $rankIndex = array_search("HIGH CARD", $this->handRank);
        } else {
            $rankIndex = array_search($hand, $this->handRank);
        }
        return $rankIndex;
    }

    public function compareTwoHands(array $a, array $b): int
    {
        foreach ($a as $i => $cardA) {
            $valueA = $this->cardValue[$cardA['value']];
            $valueB = $this->cardValue[$b[$i]['value']];

            if ($valueA > $valueB) return -1;
            if ($valueA < $valueB) return 1;
        }

        return 0;
    }

    public function compareHands(array $hands): array
    {
        usort($hands, [$this, 'compareTwoHands']);
        return $hands;
    }
}

$testHand = ['2S,4C,7S,9H,10H', '4D,5S,6S,8D,3C', '3S,JH,4S,5D,6H'];
$test = new Poker($testHand);
var_dump($test->bestHands);
