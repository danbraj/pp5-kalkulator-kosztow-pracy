<?php

namespace KKP\DataBase;

class PostgreSQL_KKPDataBase implements iKKPDataBase {

    private $connection;

    public function connectDataBase() {
        $this->connection = pg_connect(); // <- dane logowania
        if (!$this->connection) {
            echo 'Blad polaczenia!';
            exit();
        }
    }

    public function createTableIfNotExists() {
        pg_exec(
            $this->connection,
            "CREATE TABLE IF NOT EXISTS records (
            id serial PRIMARY KEY,
            contractId int8,
            netValue int8,
            grossValue int8,
            costOfEmployer int8
            )"
        );
    }

    public function deleteTable() {
        pg_exec(
            $this->connection,
            "DROP TABLE records"
        );
    }

    public function getAssocRecordById($id) {
        $query = pg_query(
            $this->connection,
            sprintf(
                'SELECT * FROM records WHERE id = %d LIMIT 1',
                $id
            )
        );
        //echo '<pre>'; print_r(pg_fetch_assoc($query)); echo '</pre>';
        return pg_fetch_assoc($query); // niepasująca tablica asocjacyjna (male litery)
    }

    public function addRecordAndGetHisId($row) {
        $result = pg_query(
            $this->connection,
            sprintf(
                'INSERT INTO records (contractId, netValue, grossValue, costOfEmployer) VALUES (%d, %d, %d, %d) RETURNING id',
                $row['contractId'],
                $row['netValue'],
                $row['grossValue'],
                $row['costOfEmployer']
            )
        );
        return pg_fetch_object($result)->id;
    }

}