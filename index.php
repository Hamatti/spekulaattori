<!DOCTYPE html>

<?php
  require('helpers.php');
  require('config.php');

  if(array_key_exists('sijoitukset', $_POST)) {
    $input = read_and_clean_input($_POST['sijoitukset']);
  } else {
    $input = array();
  }

  $points = read_csv($input_file);
  $points = add_and_sort($points, $input);

  $tournament_count = (count($input) > 0) ? '7' : '6';
?>



<html>
    <head>
        <meta charset='iso-8859-15' />
      	<meta property="og:image" content="http://poytajaakiekko.net/spjkl/gfx/spjkluusi.gif"/>
      	<meta property="og:title" content="SPJKL-EM-spekulaattori" content="http://poytajaakiekko.net/spjkl/gfx/spjkluusi.gif"/>
      	<meta property="og:description" content="Suomen pöytäjääkiekkoliiton maajoukkuepaikkojen spekulointiin tarkoitettu työkalu." />

        <title> SPJKL-<?php echo $championship; ?>-spekulaattori</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

    <div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1> SPJKL-<?php echo $championship; ?>-spekulaattori </h1>

            <p> Tämä spekulaattori on tarkoitettu työkaluksi tilastonnälkäisille pöytäjääkiekkoilijoille. Spekulaattorista läytyy ranking-kauden kuuden ensimmäisen turnauksen tulokset ja voit syöttää loppusijoituslistan haluamassasi järjestyksessä päätöskilpailusta ja näet, miten se vaikuttaa lopullisiin sijoituksiin. Ei enää hankalia Excel-tiedostoja. Tämä työkalu on koodattu aamukahvin parissa, joten siinä saattaa olla pieniä virheitä ja sen design on mitä on. Tilastojen oikeellisuutta ei myöskään taata, joten reissulippuja ei näiden pohjalta kannata tehdä. Palautetta voi antaa <a href="https://www.facebook.com/poytajaakiekko/posts/290169707773379" target=_blank>Facebookissa</a> tai sähköpostilla <code>webmaster at poytajaakiekko fi</code></p>

            <p> Taulukko päivittyy <code>Submit</code>-napin painamisen jälkeen. Pääset takaisin alkutilaan tyhjentämällä loppusijoituslistan ja painamalla <code>Submit</code> </p>
        </div>
    </div>
    <div class="row">
    <div id="preview" class="col-md-6">
    <h3> Tilanne <?php echo $tournament_count ?> turnauksen jälkeen </h3>
    <p><i> Esikatselussa mukana 30 parasta. Lopullisiin lasketaan kuitenkin kaikki pelaajat </i></p>
    <table class="table table-condensed table-hover table-striped">
        <thead>
            <tr>
                <th> Sij. </th><th>Pelaaja</th><th>Yht.</th>
                <?php foreach($tournament_names as $key) {
                  echo "<th>" . $key . "</th>";
                }?>
            </tr>
        </thead>
        <tbody>
    <?php
            $i = 1;
            foreach(array_slice($points,0, 31) as $player => $pts) {
                if($i == $cut_number) {
                  echo '<tr class="cut">';
                } else {
                  echo '<tr>';
                }
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
    <h3> Syötä loppusijoituslista </h3>
    <p> <i>Syötä tähän viimeisen turnauksen suomalaisten loppusijoituslista muodossa:</i> <br />
<pre>
    Suojanen Antti
    Lampi Ahti
    Pelkonen Jan
</pre>
    <i> Tarkista että nimet on kirjoitettu oikein eikä riveillä ole muita merkkejä.</i>
    <form name="input" action="index.php" method="post">
        <textarea class="form-control" cols="50" rows="20"  name="sijoitukset"><?php echo implode("\n", $input); ?></textarea><br />
        <input type="submit" class="btn btn-default" />
    </form>
    </div>
</div>
<hr />
<div class="row">
<div class="col-md-12">
<footer>
    <a href="http://poytajaakiekko.fi">Suomen pöytäjääkiekkoliitto ry</a>
</footer>
</div>
</div>

    </body>
</html>
