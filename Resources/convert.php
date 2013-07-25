<?php

// Script to convert Symfony YAML translation files to XLIFF.
//
// Will add a .xliff version of the given file in its directory.
//
// $ php convert.php path/to/MyBundle.en.yml

$file = $argv[1];

preg_match('&^(.*?)\.(.*?)\.yml&', $file, $matches);
list($fullName, $basePath, $targetLang) = $matches;
$filename = basename($basePath);
$dir = dirname($basePath);

$dom = new \DOMDocument('1.0');
$dom->formatOutput = true;
$dom->encoding = 'utf-8';
$dom->preserveWhitespace = true;
$rootEl = $dom->createElement('xliff');
$rootEl->setAttribute('version', '1.2');
$rootEl->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');
$dom->appendChild($rootEl);

$fileEl = $dom->createElement('file');
$fileEl->setAttribute('source-language', 'en');
$fileEl->setAttribute('target-language', $targetLang);
$fileEl->setAttribute('datatype', 'plaintext');
$fileEl->setAttribute('original', $filename.'.en.xliff');
$bodyEl = $dom->createElement('body');
$fileEl->appendChild($bodyEl);
$rootEl->appendChild($fileEl);

$h = fopen($fullName, 'r');

$prefix = '';

while ($line = fgets($h)) {
    $line = trim($line);
    if (preg_match('&^(.*?):$&', $line, $matches)) {
        $prefix = $matches[1];
        continue;
    }

    if (!$line) {
        $prefix = '';
    }

    if (!preg_match('&^(.*?):(.*)$&', $line, $matches)) {
        echo "Could not match line ". $line;
        continue;
    }
    array_shift($matches);
    list($key, $value) = $matches;

    if ($prefix) {
        $key = $prefix.'.'.$key;
    }

    $transUnitEl = $dom->createElement('trans-unit');
    $transUnitEl->setAttribute('id', $key);
    $sourceEl = $dom->createElement('source');
    $sourceEl->nodeValue = $key;
    $targetEl = $dom->createElement('target');
    $targetEl->nodeValue = trim($value);
    $transUnitEl->appendChild($sourceEl);
    $transUnitEl->appendChild($targetEl);
    $bodyEl->appendChild($transUnitEl);
}

fclose($h);

$out = $dom->saveXml();
$outfile = $dir.'/'.$filename.'.'.$targetLang.'.xliff';
file_put_contents($outfile, $out);

exit(0);
