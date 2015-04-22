<?php
/**
 * Index fail
 *
 * Index fail sisaldab k6ike vajalikku jne
 *
 * @author Marko Käär
 */

/**
 * app requiremine
 *
 */
require_once 'app.php';

/**
* Esimese funktsiooni algus, index.php enda oma>
 *
 * asd
 *
 * @package Kasutaja
*/
function index(){
    global $app;
    $app->render('index.twig');
}
$app->get('/', 'index');

/**
 * Tunniplaan
 *
 * ja selle desc
 *
 * @package Kasutaja
 */
function tunniplaan(){
    global $app;
    if(isset($_GET['klass'])) {
        $tund = ORM::for_table('tunniplaan')->where('klass', $_GET['klass'])->find_many();
        $app->render('tunniplaan.twig', array(
            'klass' => $_GET['klass'],
            'tund' => $tund,
            'paevad' => array('Esmaspäev', 'Teisipäev', 'Kolmapäev', 'Neljapäev', 'Reede')
        ));
    }
    else{
        $app->render('tunniplaan.twig');
    }
}
$app->get('/tunniplaan', 'tunniplaan');

/**
 * Menüü
 *
 * Pikk description
 *
 * @package Kasutaja
 */
function menyy(){
    global $app;
    $parser = new \Smalot\PdfParser\Parser();
    $pdf    = $parser->parseFile('http://nrg.tartu.ee/dokumendid/menyy.pdf');
    $text = $pdf->getText();
    $text = str_replace("Esmaspäev","<b> Esmaspäev</b>",$text);

    $app->render('menyy.twig', array(
        'menyy' => $text
    ));
}
$app->get('/menyy', 'menyy');

/**
 * Teated
 *
 * ja selle desc
 *
 * @package Kasutaja
 */
function teated(){
    global $app;
    $teade = ORM::for_table('teated')->order_by_desc('id')->limit(10)->find_many(2);
    $app->render('teated.twig', array(
        'teade' => $teade
    ));
}
$app->get('/teated', 'teated');


/**
 * Admin paneel
 *
 * Admin paneeli funktsioon, kontrollib kas kasutaja on sisselogitud
 *
 * @package admin-paneel
 * @subpackage Menüü
 *
 * @global object $app on defineeritud app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Seissionisse salvestatud kasutajanimi
 * @var $asdded Seissionisse salvestatud kasutaja access level - kasutaja õiguste kindlaks tegemiseks
 */
function admin(){
    global $app;
    if(isset($_SESSION['username'])){
        $app->render('admin.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access']
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
}
$app->get('/admin/', 'admin');

/**
 * Admin menüü
 *
 * pikk desc
 *
 * @package admin-paneel
 * @subpackage Menüü
 */
function admin_menyy(){
    global $app;
    if(isset($_SESSION['username'])) {
        $app->render('admin_menyy.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access']
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
}
$app->get('/admin/menyy', 'admin_menyy');

/**
 * Admin tunniplaan
 *
 * pikk descccc
 *
 * @package admin-paneel
 * @subpackage Menüü
 */
function admin_tunniplaan(){
    global $app;
    if(isset($_SESSION['username'])) {
        $app->render('admin_tunniplaan.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access']
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
}
$app->get('/admin/tunniplaan', 'admin_tunniplaan');

/**
 * Admin teated
 *
 * pikk desc
 *
 * @package admin-paneel
 * @subpackage Menüü
 */
function admin_teated(){
    global $app;
    $teade = ORM::for_table('teated')->order_by_desc('id')->limit(10)->find_many(2);
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
}
$app->get('/admin/teated', 'admin_teated');

/**
 * Admin seaded
 *
 * l desc
 *
 * @package admin-paneel
 * @subpackage Menüü
 */
function admin_seaded(){
    global $app;
    if(isset($_SESSION['username'])) {
        $kasutaja = ORM::for_table('users')->where('name',$_SESSION['username'])->find_one();
        $app->render('admin_seaded.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access'],
            'kasutaja' => $kasutaja
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
}
$app->get('/admin/seaded', 'admin_seaded');

/**
 * Admin seadete uuendamine
 *
 * ja nende desc
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_seaded_update(){
    global $app;
    if(isset($_SESSION['username'])) {
        $salt = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5); //Loob suvalise 5-margilise salti
        $password = $_POST['password'];
        $newpassword = sha1($password + $salt);

        $kasutaja = ORM::for_table('users')->where_equal('name', $_SESSION['username'])->find_one();
        $kasutaja->set('password', $newpassword);
        $kasutaja->set('salt', $salt);
        $kasutaja->save();

        $app -> redirect('/mnrg/admin/seaded');
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
}
$app->post('/admin/seaded/update', 'admin_seaded_update');

/**
 * Admin uus kasutaja
 *
 * description
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_newuser(){
    global $app;
    if(isset($_SESSION['username'])) {
        $kasutajad = ORM::for_table('users')->find_many();
        $app->render('admin_newuser.twig', array(
            'kasutajanimi' => $_SESSION['username'],
            'access' => $_SESSION['access'],
            'randomparool' => rand(100000,999999),
            'kasutaja' => $kasutajad
        ));
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
}
$app->get('/admin/new_user', 'admin_newuser');

/**
 * Admin kasutaja kustutamine
 *
 * descript
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_user_delete(){
    global $app;
    if(isset($_SESSION['username'])) {
        $kasutaja = ORM::for_table('users')
            ->where_equal('id', $_POST['id_delete'])
            ->delete_many();

        $app -> redirect('/mnrg/admin/new_user');
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
}
$app->post('/admin/new_user/delete', 'admin_user_delete');

/**
 * Admin kasutaja uuendamine
 *
 * desc
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_user_update(){
    global $app;
    if(isset($_SESSION['username'])) {
        $kasutaja = ORM::for_table('users')->where_equal('id', $_POST['id_update'])->find_one();
        $kasutaja->set('access', $_POST['access']);
        $kasutaja->save();
        $app -> redirect('/mnrg/admin/new_user');
    }
    else{
        $app -> redirect('/mnrg/admin/login');
    }
}
$app->post('/admin/new_user/update', 'admin_user_update');

/**
 * Admin kasutaja sisestamine
 *
 * desc
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_user_sisesta(){
    global $app;
    $salt = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5); //Loob suvalise 5-margilise salti
    $password = $_POST['password'];
    $newpassword = sha1($password + $salt);

    $uuskasutaja = ORM::for_table('users')->create();
    $uuskasutaja->name = $_POST['name'];
    $uuskasutaja->email = $_POST['email'];
    $uuskasutaja->password = $newpassword;
    $uuskasutaja->salt = $salt;
    $uuskasutaja->save();

    $app -> redirect('/mnrg/admin/new_user');
}
$app->post('/admin/new_user/sisesta', 'admin_user_sisesta');

/**
 * Admin login
 *
 * desc
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_login(){
    global $app;
    $app->render('admin_login.twig');
}
$app->get('/admin/login', 'admin_login');

/**
 * Admin login error
 *
 * desc
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_login_error(){
    global $app;
    $error = True;
    $app->render('admin_login.twig', array(
        'error' => $error
    ));
}
$app->get('/admin/login/error', 'admin_login_error');

/**
 * Admin teadete sisestamine
 *
 * dec
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_teated_sisesta(){
    global $app;
    $teated = ORM::for_table('teated')->create();
    $teated->username = $_SESSION['username'];
    $teated->content = $_POST['content'];
    $teated->date = date('l\, jS \of F Y H:i');
    $teated->save();

    $app -> redirect('/mnrg/admin/teated');
}
$app->post('/admin/teated/sisesta', 'admin_teated_sisesta');

/**
 * Admin teadete kustutamine
 *
 * dec
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_teated_kustuta(){
    global $app;
    $id = $_GET['id'];
    echo $id;

    $person = ORM::for_table('teated')
        ->where_equal('id', $id)
        ->delete_many();
    $app -> redirect('/mnrg/admin/teated');
}
$app->get('/admin/teated/kustuta', 'admin_teated_kustuta');

/**
 * Admin tunniplaani sisestamine
 *
 * Võtab vastu funktsioonist admin_tunniplaan() allolevad muutujad ning lisab need andmebaasi. Kui andmebaasis on
 * eelnevalt konkreetsel päeval sellel klassil tunde, kustutab need. Suunab edasi funktsiooni admin_tunniplaan().
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_POST['klass'] Sisaldab klassi nime, kuhu tunniplaan sisestatkase.
 * @var string $_POST['paev'] Sisaldab konkreetsed päeva, kuhu tunniplaan sisestatakse.
 * @var array $tunnid sisaldab stringe $_POST['tund1'] kuni $_POST['tund10'], mis omakorda sisaldavad konkreetset õppeaine nimetust.
 * @var array $ruumid sisaldab stringe $_POST['ruum1'] kuni $_POST['ruum10'], mis omakorda sisaldavad konkreetse tunni toimumise ruumi.
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_tunniplaan_sisesta(){
    global $app;
    $klass = $_POST['klass'];
    $paev = $_POST['paev'];
    $tunnid = array($_POST['tund1'],$_POST['tund2'],$_POST['tund3'],$_POST['tund4'],$_POST['tund5'],$_POST['tund6'],$_POST['tund7'],$_POST['tund8'],$_POST['tund9'],$_POST['tund10']);
    $ruumid = array($_POST['ruum1'],$_POST['ruum2'],$_POST['ruum3'],$_POST['ruum4'],$_POST['ruum5'],$_POST['ruum6'],$_POST['ruum7'],$_POST['ruum8'],$_POST['ruum9'],$_POST['ruum10']);

    //Kui on andmebaasis sellel klassil selle p2eva peal tunde, kustutab need
    $vanad_tunnid = ORM::for_table('tunniplaan')
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
}
$app->post('/admin/tunniplaan/sisesta', 'admin_tunniplaan_sisesta');

/**
 * Admin authenticate
 *
 * Admin paneelile sisselogimise autentimine. Vigade korral suunab admin_login_error() funktsiooni
 * vigade puudumise korral suunab edasi admin() funktsiooni, mis kuvab admin-paneeli pealeht.
 *
 * Kontrollib: kas kasutajanime ning parooli lahter olid täidetud, kas kasutajanimi on andmebaasis,
 * kas sisestatud parool ühtib andmebaasis oleva parooliga.
 *
 * Loob salvestab sessionisse kasutajanime ning kasutaja õiguste taseme.
 *
 * @package admin-paneel
 * @subpackage Sisu
 *
 * @var string $_POST['username'] Saadetakse funktsioonist admin_login(), sisaldab sisestatud kasutajanime.
 * @var string $_POST['password'] Saadetakse funktsioonist admin_login(), sisaldab sisestatud parooli.
 * @global object $app on defineeritud failis app.php, käivitab Slim frameworki.
 */
function admin_authenticate(){
    global $app;
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(!$username || !$password){ redirect('/mnrg/admin/login/error'); }

    $person = ORM::for_table('users')->where('name', $username)->find_one();
    $salt = $person -> salt;
    $dbpassword = $person -> password;
    $realpassword = sha1($password + $salt);

    if(!$person){ $app -> redirect('/mnrg/admin/login/error'); }
    if($dbpassword != $realpassword){ $app -> redirect('/mnrg/admin/login/error');}

    $_SESSION["username"] = $username;
    $_SESSION["access"] = $person -> access;

    $app -> redirect('/mnrg/admin/');
}
$app->post('/admin/authenticate', 'admin_authenticate');

/**
 * Admin logout
 *
 * Admin-paneelilt väljalogimine, sessiooni hävitamine, suunab edasi rakenduse pealehele.
 *
 * @package admin-paneel
 * @subpackage Sisu
 *
 * @global object $app on defineeritud failis app.php, käivitab Slim frameworki.
 * @link /mnrg/
 */
function admin_logout(){
    global $app;
    session_destroy();
    $app -> redirect('/mnrg/');
}
$app->get('/admin/logout', 'admin_logout');

/**
 * AJUTINE MENÜÜ
 *
 * Tuleb menüü läbi jsoni
 *
 * @package Menüü
 */
function ajutine_menyy() {
//    echo json_encode(
//        ['14.04.2015' => ['menyy' => 'louna', 'ohtu']]
//    );
    echo json_encode(
        ['15.04.2015' => [
            'louna' => 'kartul',
            'ohtu' => ['makaron', 'saiake', 'vesi']
        ]]
    );
}
$app->get('/ajutine-menyy', 'ajutine_menyy');

$app->run();