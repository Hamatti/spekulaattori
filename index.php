<?php

require('helpers.php');
require('stats_funcs.php');

$new = $_POST['sijoitukset'];
$new = explode("\n", $new);

if($new[0] == null) {
  $new = array();
}
$sanitized = array();
foreach($new as $name) {
    $sanitized[] = trim($name);
}

$points = read_csv('em.csv');
$new_points = $points;
$i = 1;
foreach($sanitized as $name) {
    $pts = get_point($i++);
    $new_points[$name][6] = $pts;
    unset($new_points[$name]['total']);
    $new_points[$name]['total'] = calculate_points($new_points[$name]);
}
uasort($new_points, 'sort_by_total');


?>

<doctype !html>

<html>
    <head>
        <meta charset='iso-8859-15' />
	<meta property="og:image" content="http://poytajaakiekko.net/spjkl/gfx/spjkluusi.gif"/>
	<meta property="og:title" content="SPJKL-EM-spekulaattori" content="http://poytajaakiekko.net/spjkl/gfx/spjkluusi.gif"/>
	<meta property="og:description" content="Suomen päytäjääkiekkoliiton maajoukkuepaikkojen spekulointiin tarkoitettu tyäkalu." />
        <title> SPJKL-EM-spekulaattori</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

    <div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1> SPJKL-EM-spekulaattori </h1>

            <p> Tämä spekulaattori on tarkoitettu työkaluksi tilastonnälkäisille pöytäjääkiekkoilijoille. Spekulaattorista läytyy ranking-kauden kuuden ensimmäisen turnauksen tulokset ja voit syöttää loppusijoituslistan haluamassasi järjestyksessä päätöskilpailusta ja näet, miten se vaikuttaa lopullisiin sijoituksiin. Ei enää hankalia Excel-tiedostoja. Tämä työkalu on koodattu aamukahvin parissa, joten siinä saattaa olla pieniä virheitä ja sen design on mitä on. Tilastojen oikeellisuutta ei myöskään taata, joten reissulippuja ei näiden pohjalta kannata tehdä. Palautetta voi antaa <a href="https://www.facebook.com/poytajaakiekko/posts/290169707773379" target=_blank>Facebookissa</a> tai sähköpostilla <code>webmaster at poytajaakiekko fi</code></p>

            <p> Taulukko päivittyy <code>Submit</code>-napin painamisen jälkeen. Pääset takaisin alkutilaan tyhjentämällä loppusijoituslistan ja painamalla <code>Submit</code> </p>
        </div>
    </div>
    <div class="row">
    <div id="preview" class="col-md-6">
    <h3> Tilanne <?php echo (count($new) > 0 && $new[0]) ? '7' : '6' ?> turnauksen jälkeen </h3>
    <p><i> Esikatselussa mukana 30 parasta. Lopullisiin lasketaan kuitenkin kaikki pelaajat </i></p>
    <table class="table table-condensed table-hover table-striped">
        <thead>
            <tr>
                <th> Sij. </th><th>Pelaaja</th><th>Yht.</th><th>Ou</th><th>Ma</th><th>Tu</th><th>SM</th><th>Ot</th><th>Va</th><th>TuOp</th>
            </tr>
        </thead>
        <tbody>
    <?php
            $i = 1;
            foreach(array_slice($new_points,0, 31) as $player => $pts) {
                echo '<tr>';
                echo '<td>'.$i++.'</td><td>'.$player.'</td><td>'.$pts['total'].'</td>';
                unset($pts['total']);
                foreach($pts as $tourn) {
                    echo '<td>'.$tourn.'</td>';
                }
            }
    ?>
    </table>
    </div>
    <div class="col-md-6" id="input">
    <h3> Syätä loppusijoituslista </h3>
    <p> <i>Syätä tähän Turku Openin suomalaisten loppusijoituslista muodossa:</i> <br />
<pre>
    Suojanen Antti
    Lampi Ahti
    Pelkonen Jan
</pre>
    <i> Tarkista että nimet on kirjoitettu oikein eikä riveillä ole muita merkkejä.</i>
    <form name="varkaus" action="index.php" method="post">
        <textarea class="form-control" cols="50" rows="20"  name="sijoitukset"><?php foreach($sanitized as $name) echo $name."\n" ?></textarea><br />
        <input type="submit" class="btn btn-default" />
    </form>
    </div>
</div>
<hr />
<div class="row">
<div class="col-md-12">
<footer>
    <a href="http://poytajaakiekko.fi">Suomen päytäjääkiekkoliitto ry</a>
</footer>
</div>
</div>

    </body>
</html>
