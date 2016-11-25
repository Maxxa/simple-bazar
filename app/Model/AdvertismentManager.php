<?php
namespace App\Model;

use App\Collegas\Security\CryptoService;
use Exception;
use Nette\Database\Context;
use Nette\Utils\DateTime;

class AdvertismentManager
{

    const TABLE_NAME = "advertisement";
    const SALE = "sale";

    const PURCHASE = "purchase";

    /** @var Context */
    private $database;

    private $dateParam;
    /**
     * @var CryptoService
     */
    private $cryptoService;

    public function __construct($dateParam, Context $database, CryptoService $cryptoHelper)
    {
        $this->database = $database;
        $this->dateParam = DateTime::createFromFormat('d. m. Y', $dateParam);
        $this->cryptoService = $cryptoHelper;
    }

    public function close($id)
    {
        //close row
    }

    /**
     * @param $id
     * @return \Nette\Database\Table\IRow
     */
    public function row($id)
    {
        return $this->dataAll()->get($id);
    }

    /**
     * @return \Nette\Database\Table\Selection
     */
    private function rows()
    {
        return $this->database->table(self::TABLE_NAME)
            ->where(array("enabled" => 1));
    }

    /**
     * @param null $type
     * @return \Nette\Database\Table\Selection
     */
    public function data($type = null)
    {
        $date = new DateTime();
        $date = $date->sub(new \DateInterval("P2M"));
        $rows = $this->rows()
            ->where("timestamp BETWEEN ? AND ?", $date, new DateTime());


        if ($type != null) {
            $rows->where(array("type" => $type));
        }
        return $rows->order("timestamp DESC");
    }


    /**
     * @param $data
     * @return bool|int|\Nette\Database\Table\IRow
     */
    public function insert($data)
    {
        $data['enabled'] = 1;
        return $this->database->table(self::TABLE_NAME)->insert($data);
    }

    public static function types()
    {
        return array(
            self::SALE => "Prodám",
            self::PURCHASE => "Koupím",
        );
    }

    public function deactivate($id)
    {
        $row = $this->findRowId($id);
        if ($row == null || $row == false) {
            return false;
        }
        $row->update(array("enabled" => 0));
        return true;
    }

    public function dataAll()
    {
        return $this->database->table(self::TABLE_NAME)->order("timestamp DESC");
    }

    public function findRowId($id)
    {
        $decodedId = base64_decode($id);
        if (is_numeric($decodedId)) {
            $row = $this->row($decodedId);

            if ($row != FALSE && $this->dateParam->getTimestamp() > $row->timestamp->getTimestamp()) {
                return $row;
            }
        };

        try {
            $id = $this->cryptoService->decrypt($id);
        } catch (Exception $ex) {
            return false;
        }
        return $this->row($id);
    }
}