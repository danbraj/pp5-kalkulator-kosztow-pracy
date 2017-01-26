<?php

namespace KKP;
use \PDO;

class DataBase {

    const FILE_NAME = 'moja_baza.sqlite';
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("sqlite:".self::FILE_NAME);
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }

    public function createTableIfNotExists() {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS rekordy (
            id INTEGER PRIMARY KEY,
            typ_umowy INTEGER,
            kwota_netto INTEGER,
            kwota_brutto INTEGER,
            koszt_pracodawcy INTEGER
            )"
        );
    }

    public function deleteTable() {
        $this->pdo->exec("DROP TABLE rekordy");
    }

    public function getRowById($id) {
        $query = $this->pdo->prepare('SELECT * FROM rekordy WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function addRowToDataBaseAndGetId($row) {
        $query = $this->pdo->prepare(
            'INSERT INTO rekordy (typ_umowy, kwota_netto, kwota_brutto, koszt_pracodawcy)
            VALUES (:typ_umowy, :kwota_netto, :kwota_brutto, :koszt_pracodawcy)'
        );
        $query->bindValue(':typ_umowy', $row['typ_umowy'], PDO::PARAM_INT);
        $query->bindValue(':kwota_netto', $row['kwota_netto'], PDO::PARAM_INT);
        $query->bindValue(':kwota_brutto', $row['kwota_brutto'], PDO::PARAM_INT);
        $query->bindValue(':koszt_pracodawcy', $row['koszt_pracodawcy'], PDO::PARAM_INT);
        $query->execute();
        return $this->pdo->lastInsertId();
    }

}