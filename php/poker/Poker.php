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
        // Build details for each hand: original string, rank score, and sorted cards for tiebreak
        $handsDetails = [];

        foreach ($hands as $handStr) {
            $handValue = $this->evaluateHand($handStr);
            $score     = $this->getRank($handValue);

            $cards = $this->splitHandTocard($handStr);

            // Sort cards LOW -> HIGH (so index 4 is highest, matching your compareTwoHands after reverse fix below)
            usort($cards, fn($a, $b) => $this->cardValue[$a['value']] <=> $this->cardValue[$b['value']]);

            // For comparing highest-first, we want HIGH -> LOW
            $cardsDesc = array_reverse($cards);

            $handsDetails[] = [
                'hand'      => $handStr,
                'score'     => $score,      // lower is better in your handRank array
                'cardsDesc' => $cardsDesc,  // for tie-breaking
            ];
        }

        // 1) Find best (minimum) score (ROYAL FLUSH = 0 is best, HIGH CARD = 9 is worst)
        $bestScore = min(array_column($handsDetails, 'score'));

        // 2) Keep only hands with that best score
        $candidates = array_values(array_filter(
            $handsDetails,
            fn($h) => $h['score'] === $bestScore
        ));

        // 3) If only one candidate, it's the winner
        if (count($candidates) === 1) {
            $this->bestHands = [$candidates[0]['hand']];
            return;
        }

        // 4) Otherwise sort candidates by tie-breaker (highest cards etc.)
        usort($candidates, function ($x, $y) {
            return $this->compareTwoHands($x['cardsDesc'], $y['cardsDesc']);
        });

        // After sorting, first one is best. Collect all ties with it.
        $best = $candidates[0];
        $this->bestHands = [$best['hand']];

        for ($i = 1; $i < count($candidates); $i++) {
            if ($this->compareTwoHands($candidates[$i]['cardsDesc'], $best['cardsDesc']) === 0) {
                $this->bestHands[] = $candidates[$i]['hand'];
            } else {
                break;
            }
        }
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

$hands = ['4D,5S,6S,8D,3C', '2S,4C,7S,9H,10H', '3S,4S,5D,6H,JH', '3H,4H,5C,6C,JD'];
$test = new Poker($hands);
var_dump($test->bestHands);
