<?php
class AB
{
    //adattagok
    private $host = "localhost";
    private $felhasznalo = "root";
    private $jelszo = "";
    private $abNev = "magyar_kartya";
    private $kapcsolat;

    //konstruktor
    public function __construct()
    {
        //létrehozzuk a kapcsolatot
        $this->kapcsolat = new mysqli($this->host, $this->felhasznalo, $this->jelszo, $this->abNev);

        $this->kapcsolat->query("SET NAMES UTF8");
    }

    //tagfüggvények
    public function feltoltes($tabla, $mezo1, $mezo2)
    {
        $szinekSzama = $this->meret("szin");
        $formakSzama = $this->meret("forma");
        for ($i = 1; $i <= $szinekSzama; $i++) {
            for ($j = 1; $j <= $formakSzama; $j++) {
                $sql = "INSERT INTO kartya($mezo1, $mezo2) VALUES ('$j','$i')";
                $siker = $this->kapcsolat->query($sql);
                echo $siker ? "siker" : "nem siker";
            }
        }
    }

    public function bezar()
    {
        $this->kapcsolat->close();
    }

    public function meret($tabla)
    {
        $sql = "SELECT * FROM $tabla";
        return $this->kapcsolat->query($sql)->num_rows;
    }


    public function adatLeker($kep, $szin)
    {
        $sql = "SELECT $kep FROM $szin";
        return $this->kapcsolat->query($sql);
    }

    public function megjelenit($matrix)
    {
        echo "<table border=1>
                <th>Név</th><th>Kép</th>";
        while ($sor = $matrix->fetch_row()) {
            echo "<tr>
                <td>$sor[1]</td>
                <td><img src='forras/$sor[0]'></td>
            </tr>";
        }
        echo "</table>";
    }


    public function oszlopLeker2($oszlop1, $oszlop2, $tabla)
    {
        $sql = "SELECT $oszlop1, $oszlop2 FROM $tabla";
        $matrix = $this->kapcsolat->query($sql);
        return $matrix;
    }

    public function megjelenitTablazatKulcsokkal($matrix, $oszlop1, $oszlop2)
    {
        while ($row = $matrix->fetch_assoc()) {
            echo "<tr>
                    <td>
                        <p>$row[$oszlop1]</p>
                    </td>
                    <td>
                        <img src='forras/$row[$oszlop2]' alt='$row]$oszlop2]'>
                    </td>
                </tr>";
        }
    }

    public function beszuras($tabla, $oszlop, $tabla2, $oszlop2, $ertek)
    {
        $szinMeret = $this->meret($tabla2);
        for ($i = 1; $i <= $szinMeret; $i++) {
            $sql = "INSERT INTO $tabla($oszlop, $oszlop2) VALUES ('$ertek','$i')";
            $this->kapcsolat->query($sql);
        }
    }

    public function torles($tabla, $oszlop, $tablaHivatkozott, $hivatkozottOszlop, $mit)
    {
        $sql = "DELETE FROM $tabla WHERE $oszlop in (SELECT $oszlop FROM $tablaHivatkozott WHERE $hivatkozottOszlop = '$mit')";
        $this->kapcsolat->query($sql);
    }

    public function megjelenitKartyaTabla()
    {
        $sql = "SELECT f.formaAzon, f.szoveg 
                FROM forma AS f 
                WHERE f.formaAzon IN (SELECT DISTINCT formaAzon FROM kartya) 
                ORDER BY f.formaAzon";
        $result = $this->kapcsolat->query($sql);
        $cardTypes = array();
        while ($row = $result->fetch_assoc()) {
            $cardTypes[$row['formaAzon']] = $row['szoveg'];
        }
        $sql = "SELECT szAzon, nev, kep FROM szin ORDER BY szAzon";
        $resultSuits = $this->kapcsolat->query($sql);
        $suits = array();
        while ($row = $resultSuits->fetch_assoc()) {
            $suits[$row['szAzon']] = $row;
        }
        $cards = array();
        $sql = "SELECT formaAzon, szinAzon FROM kartya";
        $resultCards = $this->kapcsolat->query($sql);
        while ($row = $resultCards->fetch_assoc()) {
            $cards[$row['szinAzon']][$row['formaAzon']] = true;
        }
        echo "<table border='1' style='border-collapse: collapse; text-align: center;'>";
        echo "<tr><th>Szin</th>";
        foreach ($cardTypes as $formaAzon => $szoveg) {
            echo "<th>$szoveg</th>";
        }
        echo "</tr>";
        foreach ($suits as $suitId => $suitData) {
            echo "<tr>";
            echo "<td>" . $suitData['nev'] . "<br><img src='forras/" . $suitData['kep'] . "' alt='" . $suitData['nev'] . "' width='50'></td>";
            foreach ($cardTypes as $formaAzon => $szoveg) {
                echo "<td>";
                if (isset($cards[$suitId][$formaAzon])) {
                    echo "<img src='forras/" . $suitData['kep'] . "' alt='$szoveg' width='50'>";
                } else {
                    echo "-";
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    


    public function modosit($tabla, $hol, $uj, $regi)
    {
        $sql =  "UPDATE $tabla SET $hol='$uj' WHERE nev = '$regi'";
    }
}
