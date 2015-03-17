<?php
require_once 'app.php';


$app->get('/', function() use ($app){
    $app->render('index.twig');
});

$app->get('/tunniplaan', function () use ($app){
    $app->render('tunniplaan.twig');
});

$app->get('/menyy', function () use ($app){
    $parser = new \Smalot\PdfParser\Parser();
    $pdf    = $parser->parseFile('http://nrg.tartu.ee/dokumendid/menyy.pdf');

    $text = $pdf->getText();

    $text = str_replace("Esmaspäev","<b>Esmaspäev</b>",$text);

    $app->render('menyy.twig', array(
        'menyy' => $text
    ));
});

$app->get('/teated', function () use ($app){
    $app->render('teated.twig');
});

//mois22
$app->run();