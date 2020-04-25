<?php

class CafeteriaEntity extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = "cafeterias";
    }
}
