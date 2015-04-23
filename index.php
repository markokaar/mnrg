<?php
/**
 * Index fail
 *
 * Index fail sisaldab kogu rakenduse sisu.
 *
 * @author Marko Käär
 */

/**
 * app.php requiremine
 */
require_once 'app.php';

/**
 * Index - pealeht
 *
 * Kasutaja jaoks kuvatakse välja rakenduse pealeht, milleks on index.twig
 *
 * @global object $app on defineeritud app.php, käivitab Slim frameworki.
 * @link /mnrg/
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
 * Kuvab kasutaja jaoks välja valitud klassi terve nädala tunniplaani.
 *
 * @global object $app on defineeritud app.php, käivitab Slim frameworki.
 * @var string $_GET['klass'] Algselt puudu, kasutaja valib oma klassi menyy.twig failis ja määrab sellega muutuja väärtuse.
 * @link /mnrg/tunniplaan
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
 * Kuvab kasutaja jaoks andmebaasist välja menüü. - PUUDU
 *
 * @global object $app on defineeritud app.php, käivitab Slim frameworki.
 * @link /mnrg/tunniplaan
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
 * Kuvab andmebaasist välja kasutaja jaoks viimased 10 teadet.
 *
 * @global object $app on defineeritud app.php, käivitab Slim frameworki.
 * @link /mnrg/menyy
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
 * @link /mnrg/admin/
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
 * Kuvab admin_menyy.twig, kus on ??????????? võimalus. Kui admin kasutaja ei ole sisse logitud
 * (pole määratud $_SESSION['username']) siis suunab funktsiooni admin_login().
 *
 * @link /mnrg/admin/menyy
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud kasutaja nimi, kontrollimaks kas adminkasutaja on
 * sisse logitud, et kuvada välja vajalikku infot.
 * @var integer $_SESSION['access'] Sessionisse salvestatud sisse logitud kasutaja access level(õiguste tase).
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
 * Kuvab admin_tunniplaan.twig, kus on tunniplaani lisamise võimalus. Kui admin kasutaja ei ole sisse logitud
 * (pole määratud $_SESSION['username']) siis suunab funktsiooni admin_login().
 *
 * @link /mnrg/admin/tunniplaan
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud kasutaja nimi, kontrollimaks kas adminkasutaja on
 * sisse logitud, et kuvada välja vajalikku infot.
 * @var integer $_SESSION['access'] Sessionisse salvestatud sisse logitud kasutaja access level(õiguste tase).
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
 * Kuvab admin_teated.twig, kus on teadete lisamise, kustutamise võimalus. Kui admin kasutaja ei ole sisse logitud
 * (pole määratud $_SESSION['username']) siis suunab funktsiooni admin_login().
 *
 * @link /mnrg/admin/teated
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud kasutaja nimi, kontrollimaks kas adminkasutaja on
 * sisse logitud, et kuvada välja vajalikku infot.
 * @var integer $_SESSION['access'] Sessionisse salvestatud sisse logitud kasutaja access level(õiguste tase).
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
 * Kuvab admin_seaded.twig, kus on kasutaja parooli muutmise võimalus. Kui admin kasutaja ei ole sisse logitud
 * (pole määratud $_SESSION['username']) siis suunab funktsiooni admin_login().
 *
 * @link /mnrg/admin/seaded
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud kasutaja nimi, kontrollimaks kas adminkasutaja on
 * sisse logitud, et kuvada välja vajalikku infot.
 * @var integer $_SESSION['access'] Sessionisse salvestatud sisse logitud kasutaja access level(õiguste tase).
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
 * Admin-paneeli seaded, uuendab sisse logitud kasutaja parooli ning suunab edasi funktsiooni admin_seaded().
 * Kontrollib ka kas kasutaja on sisselogitud, kui mitte siis suunab funktsiooni admin_login().
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud kasutaja nimi, kontrollimaks kas adminkasutaja on
 * sisse logitud, et kuvada välja vajalikku infot.
 * @var string $_POST['password'] Sisestatud kasutaja uus parool, saadetakse funktsioonist admin_seaded().
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
 * Admin paneelil menüüs "Uus kasutaja". Kuvab välja admin_newuser.twig Võimaldab lisada, kustutada, uuendada
 * admin-kasutajat. Kuvab välja kõik admin-kasutajad.
 *
 * @link /mnrg/admin/newuser
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud kasutaja nimi, kontrollimaks kas adminkasutaja on
 * sisse logitud, et kuvada välja vajalikku infot.
 * @var integer $_SESSION['access'] Sessionisse salvestatud sisse logitud kasutaja access level(õiguste tase).
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
 * Funktsiooniast admin_newuser() saadetakse vajalikud muutujad, kustutatakse andmebaasist valitud id-ga kasutaja.
 * Kontrollib ka kas kasutaja on sisselogitud, kui mitte, siis muudatusi ei tehta ja suunatakse funktsiooni admin_login().
 * Suunab edasi funktsiooni admin_newuser().
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud kasutaja nimi, kontrollimaks kas adminkasutaja on
 * sisse logitud, et infot andmebaasis muuta.
 * @var integer $_POST['id_delete'] Kustutatava kasutaja id andmebaasis. Saadetakse funktsioonist admin_newuser().
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
 * Muudab kasutaja access(ligipääsu) õigusi. Vajalikud muutujad saadakse funktsioonist admin_newuser(). Kontrollib ka
 * kas kasutaja on sisselogitud, kui mitte, siis muudatusi ei tehta ja suunatakse funktsiooni admin_login().
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud kasutaja nimi, kontrollimaks kas adminkasutaja on
 * sisse logitud, et infot andmebaasis muuta.
 * @var integer $_POST['id_update'] Muudetava kasutaja id andmebaasis.
 * @var integer $_POST['access'] Muudetava kasutaja uus access level 1-Tavaline admin, 3-Kõikide õigustega admin.
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
 * Saab allolevad muutujad funktsioonist admin_newuser() ning lisab sisestatud kasutaja ning tema andmed andmebaasi.
 * Kasutaja suunatakse edasi funktsiooni admin_newuser().
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_POST['name'] Sisaldab sisestatud kasutaja kasutajanime.
 * @var string $_POST['email'] Sisaldab sisestatud kasutaja emaili.
 * @var string $_POST['password'] Sisaldab sisestatud kasutaja parooli.
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
 * Kuvab välja admin_login.twig
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @link /mnrg/admin/login
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
 * Kuvab välja admin_login.twig koos erroriga.
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var bool error Väärtus vaikimisi True, saadetakse faili admin_login.twig, kus kuvatakse välja, et sisselogimine
 * ebaõnnestus.
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
 * Võtab vastu kaks muutujat kasutajanimi ja teate sisu ning lisab need andmebaasi. Lisab juurde kuupäeva ning kellaaja.
 * Kasutaja suunatakse tagasi funktsiooni admin_teated().
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var string $_SESSION['username'] Sessionisse salvestatud sisse logitud kasutaja nimi.
 * @var string $_POST['content'] Saadetakse funktsioonist admin_teated(), sisaldab sisestatava teate sisu.
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
 * Võtab vastu kustutava teate id, kustutab andmebaasist selle id all oleva teate.
 * Suunab kasutaja tagasi funktsiooni admin_teated()
 *
 * @global object $app On defineeritud failis app.php, käivitab Slim frameworki.
 * @var integer $_GET['id'] Sisaldab kustutatava teate id-d, saadetakse funktsioonist admin_teated().
 *
 * @package admin-paneel
 * @subpackage Sisu
 */
function admin_teated_kustuta(){
    global $app;
    $id = $_GET['id'];

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