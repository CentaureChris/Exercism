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

    public function __construct(array $hands) 
    {
    }

    public function splitHandTocard(string $hand)
    {
        $cardList = [];
        $cards = explode(",", $hand);

        foreach ($cards as $card) {
            $c = [];
            if (strlen($card) == 3) {
                $c["value"] = $card[0] . $card[1];
                $c["color"] = $card[2];
            } else {
                $c["value"] = $card[0];
                $c["color"] = $card[1];
            }
            array_push($cardList, $c);
        }
        return $cardList;
    }

    public function getHandType(string $hand): string
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
                $handValue = "4 OF KIND";
            case $trips === 1 && $pairs === 1:
                $handValue = "FULL";
            case $trips === 1:
                $handValue = "3 OF KIND";
            case $pairs === 2:
                $handValue = "TWO PAIR";
            case $pairs === 1:
                $handValue = "PAIR";
            default:
                $handValue = "";
        }

        if ($handValue != "") {
            return $handValue;
        }

        $i = 0;
        while ($hand[$i] <= count($hand)) {
            if ($hand[$i + 1] == $hand[$i]) {
                $handValue = 'COLOR';
            }
        }
    }

    public function getHandValue(array $hand): int 
    {
        
    }
    

}

$testHand = ['2S,4H,4C,4D,3H'];
$test = new Poker($testHand);

$test->splitHandTocard($testHand[0]);
var_dump($test->getHandType($testHand[0]));
