<?php

namespace KKP\DataBase;

interface iKKPDataBase {
    public function connectDataBase();
    public function createTableIfNotExists();
    public function deleteTable();
    public function getAssocRecordById($id);
    public function addRecordAndGetHisId($row);
}