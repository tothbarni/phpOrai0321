<?php
    include_once "AB.php";
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kártya</title>
</head>
<body>
    <?php
        $adatbazis = new AB();
        
        if ($adatbazis->meret("kartya") == 0) {
            $adatbazis->feltoltes("kartya", "formaAzon", "szinAzon");

            
        }

        $matrix = $adatbazis->adatLeker("kep", "szin");
        //$adatbazis->megjelenit($matrix);


        $matrix2 = $adatbazis->oszlopLeker2("kep", "nev", "szin");
        $adatbazis->megjelenit($matrix2);
        $adatbazis->modosit("szin", "nev", "piros", "vörös");
        $adatbazis->megjelenitTablazatKulcsokkal($matrix, $kep, $nev);
        $adatbazis->torles("kartya", "formaAzon", "forma", "szoveg", "alsó");
        $adatbazis->bezar();
    ?>
</body>
</html>