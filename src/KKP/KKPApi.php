<?php

namespace KKP;

class KKPApi {

    static $aUmowy = array("Umowa zlecenie", "Umowa o dzielo", "Umowa o prace");

    static function wyswietlOpisDzialania($sNazwaPliku) {
        echo "\n\tSkladnia:\tphp {$sNazwaPliku} [kwota_netto] [typ_umowy] [zapis = false]\n";
        echo "\t\t\tphp {$sNazwaPliku} -rekord [id]\n\n";
        echo "\tid\t\tliczba\n";
        echo "\tkwota_netto\tliczba\n";
        echo "\ttyp_umowy\t0 => Umowa zlecenie\n";
        echo "\t\t\t1 => Umowa o dzielo\n";
        echo "\t\t\t2 => Umowa o prace\n";
        echo "\tzapis\t\twartosc logiczna (true, 1, false, 0)\n";
    }
}