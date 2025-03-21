<?php
class AB {
    //adattagok
    private $host = "localhost";
    private $felhasznalo = "root";
    private $jelszo = "";
    private $abNev = "magyar_kartya";
    private $kapcsolat;

    //konstruktor
    public function __construct(){
        //létrehozzuk a kapcsolatot
        $this->kapcsolat = new mysqli($this->host, $this->felhasznalo, $this->jelszo, $this->abNev);

        $this->kapcsolat->query("SET NAMES UTF8");
    }
    
    //tagfüggvények
    public function feltoltes($tabla, $mezo1, $mezo2) {
        $szinekSzama = $this->meret("szin");
        $formakSzama = $this->meret("forma");
        for ($i=1; $i <= $szinekSzama; $i++) { 
            for ($j=1; $j <= $formakSzama; $j++) { 
                $sql = "INSERT INTO kartya($mezo1, $mezo2) VALUES ('$j','$i')";
                $siker = $this->kapcsolat->query($sql);
                echo $siker?"siker":"nem siker";
            }
        }
    }

    public function bezar() {
        $this->kapcsolat->close();
    }

    public function meret($tabla) {
        $sql = "SELECT * FROM $tabla";
        return $this->kapcsolat->query($sql)->num_rows;
    }


    public function adatLeker($kep,$szin) {
        $sql = "SELECT $kep FROM $szin";
        return $this->kapcsolat->query($sql);
    }

    public function megjelenit($matrix) {
        echo "<table border=1>
                <th>Név</th><th>Kép</th>";
        while($sor = $matrix->fetch_row()) {
            echo "<tr>
                <td>$sor[1]</td>
                <td><img src='forras/$sor[0]'></td>
            </tr>";
        }
        echo "</table>";
    }


    public function oszlopLeker2($oszlop1,$oszlop2, $tabla) {
        $sql = "SELECT $oszlop1, $oszlop2 FROM $tabla";
        $matrix = $this->kapcsolat->query($sql);
        return $matrix;
    }

    public function megjelenitTablazatKulcsokkal($matrix, $oszlop1, $oszlop2){
        while ($row = $matrix->fetch_assoc()) {
            echo "<tr><td><p>$row[$oszlop1]</p></td><td><img src='forras/$row[$oszlop2]' alt='$row]$oszlop2]'></td></tr>";
        }
    }

    public function torles($tabla, $oszlop, $tablaHivatkozott, $hivatkozottOszlop, $mit){
        $sql = "DELETE FROM $tabla WHERE $oszlop in (SELECT $oszlop FROM $tablaHivatkozott WHERE $hivatkozottOszlop = '$mit')";
        $this->kapcsolat->query($sql);
    }


    public function modosit($tabla, $hol, $uj, $regi) {
        $sql =  "UPDATE $tabla SET $hol='$uj' WHERE nev = '$regi'";
    }
    

}
?>