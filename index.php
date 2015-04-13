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

$app->get('/admin/', function () use ($app){
    $app->render('admin.twig');
});

$app->get('/admin/menyy', function () use ($app){
    $app->render('menyy.twig');
});

$app->get('/admin/tunniplaan', function () use ($app){
    $app->render('menyy.twig');
});

$app->get('/admin/teated', function () use ($app){
    $app->render('menyy.twig');
});

$app->get('/admin/seaded', function () use ($app){
    $app->render('menyy.twig');
});

$app->get('/admin/logout', function () use ($app){
    header("Location: index.php");
});

$app->get('/ajutine-menyy', function() {
    echo json_encode([
        '14.04.2015' => ['menyy']
    ]);
});


$app->run();