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


//Admin-paneel
$app->get('/admin/', function () use ($app){
    if(isset($_SESSION['username'])){
        $app->render('admin.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access']
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }

});

$app->get('/admin/menyy', function () use ($app){
    if(isset($_SESSION['username'])) {
        $app->render('menyy.twig');
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/tunniplaan', function () use ($app){
    if(isset($_SESSION['username'])) {
        $app->render('menyy.twig');
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/teated', function () use ($app){
    if(isset($_SESSION['username'])) {
        $app->render('menyy.twig');
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/seaded', function () use ($app){
    if(isset($_SESSION['username'])) {
        $app->render('menyy.twig');
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/new_user', function () use ($app){
    if(isset($_SESSION['username'])) {
        $app->render('menyy.twig');
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/login', function () use ($app){
    $app->render('admin_login.twig');
});

$app->get('/admin/login/error', function () use ($app){
    $error = True;

    $app->render('admin_login.twig', array(
        'error' => $error
    ));
});

$app->post('/admin/authenticate', function () use ($app){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(!$username || !$password){ redirect('/mnrg/admin/login/error'); }

    $person = ORM::for_table('users')->where('name', $username)->find_one();

    $salt = $person -> salt;
    $dbpassword = $person -> password;

    $realpassword = sha1($password + $salt);

    if(!$person){$app -> redirect('/mnrg/admin/login/error');}
    if($dbpassword != $realpassword){ $app -> redirect('/mnrg/admin/login/error');}

    $_SESSION["username"] = $username;
    $_SESSION["access"] = $person -> access;

    $app -> redirect('/mnrg/admin/');
});

$app->get('/admin/logout', function () use ($app){
    session_destroy();
    $app -> redirect('/mnrg/');
});

$app->get('/ajutine-menyy', function() {
    echo json_encode([
        '14.04.2015' => ['menyy']
    ]);
});


$app->run();