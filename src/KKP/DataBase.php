<?php

namespace KKP;
use \PDO;

class DataBase {

    static function connectDataBase() {
        try {
            $pdo = new PDO("sqlite:baza.sqlite");
            return $pdo;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }

    static function createTableIfNotExists($pdo) {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS rekordy (
            id INTEGER PRIMARY KEY,
            typ_umowy INTEGER,
            kwota_netto INTEGER,
            kwota_brutto INTEGER,
            koszt_pracodawcy INTEGER
            )"
        );
    }

    static function selectRow($pdo, $id) {
        $query = $pdo->prepare('SELECT * FROM rekordy WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    static function addRowToDataBaseAndReturnId($pdo, $idUmowy, $kwotaNetto, $kwotaBrutto, $kosztPracodawcy) {
        $query = $pdo->prepare(
            'INSERT INTO rekordy (typ_umowy, kwota_netto, kwota_brutto, koszt_pracodawcy)
            VALUES (:typ_umowy, :kwota_netto, :kwota_brutto, :koszt_pracodawcy)'
        );
        $query->bindValue(':typ_umowy', $idUmowy, PDO::PARAM_INT);
        $query->bindValue(':kwota_netto', $kwotaNetto, PDO::PARAM_INT);
        $query->bindValue(':kwota_brutto', $kwotaBrutto, PDO::PARAM_INT);
        $query->bindValue(':koszt_pracodawcy', $kosztPracodawcy, PDO::PARAM_INT);
        $query->execute();
        return $pdo->lastInsertId();
    }

}