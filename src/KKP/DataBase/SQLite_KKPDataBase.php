<?php

namespace KKP\DataBase;
use \PDO;

class SQLite_KKPDataBase implements iKKPDataBase {

    private $pdo;

    public function connectDataBase() {
        try {
            $this->pdo = new PDO("sqlite:savedCalculations.sqlite");
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }

    public function createTableIfNotExists() {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS records (
            id INTEGER PRIMARY KEY,
            contractId INTEGER,
            netValue INTEGER,
            grossValue INTEGER,
            costOfEmployer INTEGER
            )"
        );
    }

    public function deleteTable() {
        $this->pdo->exec("DROP TABLE records");
    }

    public function getAssocRecordById($id) {
        $query = $this->pdo->prepare('SELECT * FROM records WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function addRecordAndGetHisId($row) {
        $query = $this->pdo->prepare(
            'INSERT INTO records (contractId, netValue, grossValue, costOfEmployer)
            VALUES (:contractId, :netValue, :grossValue, :costOfEmployer)'
        );
        $query->bindValue(':contractId', $row['contractId'], PDO::PARAM_INT);
        $query->bindValue(':netValue', $row['netValue'], PDO::PARAM_INT);
        $query->bindValue(':grossValue', $row['grossValue'], PDO::PARAM_INT);
        $query->bindValue(':costOfEmployer', $row['costOfEmployer'], PDO::PARAM_INT);
        $query->execute();
        return $this->pdo->lastInsertId();
    }

}