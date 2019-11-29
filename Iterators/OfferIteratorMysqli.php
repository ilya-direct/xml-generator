<?php


namespace Yml\Iterators;


use Yml\Models\IOfferIterator;
use Yml\Models\Offer;
use mysqli;
use mysqli_result;

class OfferIteratorMysqli implements IOfferIterator
{
    /** @var mysqli */
    private $mysqli;
    /** @var mysqli_result */
    private $mysqliResult;

    private $current;
    private $idx;

    private $totalFetchTime;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function current(): ?Offer
    {
        $data = $this->current;

        if ($data === null) {

            return null;
        }

        $offer = (new Offer())
            ->setId($data['id'])
            ->setAvailable($data['available'])
            ->setUrl($data['url'])
            ->setPrice($data['price'])
            ->setCategoryId($data['category_id']);

        return $offer;
    }

    public function next()
    {
        $this->current = $this->fetch();
        $this->idx++;

        return $this->current();
    }

    public function key()
    {
        return $this->idx;
    }

    public function valid()
    {
        return $this->current() != null;
    }

    public function rewind()
    {
        $time = microtime(true);
        $this->mysqli->real_query('SELECT id, available, price, url, category_id FROM offer ORDER BY url');
        /**
         * use_result - returns cursor, so it will not load memory at all (opposite store_result - load all result in memory)
         * NB! if MyIsam is uses use_result will block table(or block of rows) for update (use InnoDB instead)
         * 
         * Using Mysql PDO will load all result in memory too. So do not use it
         */
        $this->mysqliResult = $this->mysqli->use_result();
        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");

        $this->current = $this->fetch();
        $this->idx = 0;
        $this->totalFetchTime = 0;
    }

    private function fetch()
    {
        $time = microtime(true);
        $data = $this->mysqliResult->fetch_assoc();
        $this->mysqli->next_result();
        $this->totalFetchTime += microtime(true) - $time;

        if (is_null($data)) {

            print "\nTotal fetch time: {$this->totalFetchTime}\n";
        }

        return $data;
    }
}