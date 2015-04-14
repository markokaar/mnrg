<?php
require_once 'app.php';

$app->get('/', function() use ($app){
    $app->render('index.twig');
});

$app->get('/tunniplaan', function () use ($app){
    if(isset($_GET['klass'])) {
        $tund = ORM::for_table('tunniplaan')->where('klass', $_GET['klass'])->find_many();
        $app->render('tunniplaan.twig', array(
            'klass' => $_GET['klass'],
            'tund' => $tund,
            'paevad' => array('Esmaspaev', 'Teisipaev', 'Kolmapaev', 'Neljapaev', 'Reede')
        ));
    }
    else{
        $app->render('tunniplaan.twig');
    }
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
    $teade = ORM::for_table('teated')->find_many(2);
    $app->render('teated.twig', array(
        'teade' => $teade
    ));
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
        $app->render('admin_menyy.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access']
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/tunniplaan', function () use ($app){
    if(isset($_SESSION['username'])) {
        $app->render('admin_tunniplaan.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access']
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/teated', function () use ($app){
    $teade = ORM::for_table('teated')->find_many();
    if(isset($_SESSION['username'])) {
        $app->render('admin_teated.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access'],
            'teade' => $teade
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/seaded', function () use ($app){
    if(isset($_SESSION['username'])) {
        $app->render('admin_seaded.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access']
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
});

$app->get('/admin/new_user', function () use ($app){
    if(isset($_SESSION['username'])) {
        $app->render('admin_newuser.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access']
        ));
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

$app->post('/admin/teated/sisesta', function () use ($app){
    $teated = ORM::for_table('teated')->create();

    $teated->username = $_SESSION['username'];
    $teated->content = $_POST['content'];
    $teated->date = date('l\, jS \of F Y H:i');
    $teated->save();

    $app -> redirect('/mnrg/admin/teated');
});


$app->post('/admin/tunniplaan/sisesta', function () use ($app){
    $klass = $_POST['klass'];
    $paev = $_POST['paev'];

    $tunnid = array($_POST['tund1'],$_POST['tund2'],$_POST['tund3'],$_POST['tund4'],$_POST['tund5'],$_POST['tund6'],$_POST['tund7'],$_POST['tund8'],$_POST['tund9'],$_POST['tund10']);
    $ruumid = array($_POST['ruum1'],$_POST['ruum2'],$_POST['ruum3'],$_POST['ruum4'],$_POST['ruum5'],$_POST['ruum6'],$_POST['ruum7'],$_POST['ruum8'],$_POST['ruum9'],$_POST['ruum10']);
    $tund1 = $_POST['tund1'];

    //Kui on andmebaasis sellel klassil selle p2eva peal tunde, kustutab need
    $person = ORM::for_table('tunniplaan')
        ->where_equal('klass', $klass, 'paev', $paev)
        ->delete_many();

    //Lisab andmebaasi uued andmed
    for( $n=0; $n<=9; $n++ ){
        $tunniplaan = ORM::for_table('tunniplaan')->create();
        if($tunnid[$n] != '') {
            $tunniplaan->klass = $klass;
            $tunniplaan->paev = $paev;
            $tunniplaan->username = $_SESSION['username'];
            $tunniplaan->tund_nr = $n + 1;
            $tunniplaan->tund = $tunnid[$n];
            $tunniplaan->ruum = $ruumid[$n];
            $tunniplaan->save();
        }
    }
    $app -> redirect('/mnrg/admin/tunniplaan');
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