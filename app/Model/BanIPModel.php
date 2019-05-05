<?php

namespace App\Model;

use Nette\Database\Context;
use Nette\Database\Table\Selection;

class BanIPModel
{


    const TABLE_NAME = "ban_ip";
    const IP = "ip";

    /** @var Context */
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function isBan($ip)
    {
        $row = $this->get($ip)->fetch();
        return $row != null && $row;

    }

    public function ban($ip)
    {
        if ($this->isBan($ip) == false) {
            $this->table()->insert(array('ip' => $ip));
        }
    }

    public function removeBan($ip)
    {
        $row = $this->get($ip);
        if ($row) {
            $row->delete();
        }
    }

    /**
     * @param $ip
     * @return Selection
     */
    public function get($ip)
    {
        return $this->table()->where(array('ip' => $ip));
    }

    public function table()
    {
        return $this->database->table(self::TABLE_NAME);
    }

}