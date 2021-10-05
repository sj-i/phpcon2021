<?php
/* The Computer Language Benchmarks Game
   https://salsa.debian.org/benchmarksgame-team/benchmarksgame/

   regex-dna program contributed by Jesse Millikan
   Base on the Ruby version by jose fco. gonzalez
   fixed by Matthew Wilson
   ported to Node.js and sped up by Roman Pletnev
   converted from regex-dna program
   fixed by Josh Goldfoot
   multi thread by Andrey Filatkin
   sequential by Isaac Gouy
   translated to PHP by sj-i
*/

const REGEXPS = [
    'agggtaaa|tttaccct',
    '[cgt]gggtaaa|tttaccc[acg]',
    'a[act]ggtaaa|tttacc[agt]t',
    'ag[act]gtaaa|tttac[agt]ct',
    'agg[act]taaa|ttta[agt]cct',
    'aggg[acg]aaa|ttt[cgt]ccct',
    'agggt[cgt]aa|tt[acg]accct',
    'agggta[cgt]a|t[acg]taccct',
    'agggtaa[cgt]|[acg]ttaccct',
];

function mainThread() {
    $data = file_get_contents('php://stdin');
    $initialLen = strlen($data);

    $data = preg_replace('/^>.*$|\n/mS', '', $data);
    $cleanedLen = strlen($data);

    foreach (REGEXPS as $re) {
        echo $re, ' ', preg_match_all('/' . $re . '/iS', $data, $_), PHP_EOL;
    }

    $tmp = preg_replace('/tHa[Nt]/S', '<4>', $data);
    $tmp = preg_replace('/aND|caN|Ha[DS]|WaS/S', '<3>', $tmp);
    $tmp = preg_replace('/a[NSt]|BY/S', '<2>', $tmp);
    $tmp = preg_replace('/<[^>]*>/S', '|', $tmp);
    $tmp = preg_replace('/\\|[^|][^|]*\\|/S', '-', $tmp);
    $endLen = strlen($tmp);

    echo PHP_EOL,
        $initialLen, PHP_EOL,
        $cleanedLen, PHP_EOL,
        $endLen, PHP_EOL
    ;
}
mainThread();