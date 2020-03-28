<?php

namespace Yml\Iterators;

use Phalcon\Db;
use Yml\Models\IOfferIterator;
use Yml\Models\Offer;
use Phalcon\Db\ResultInterface;
use Phalcon\Db\AdapterInterface;

class OfferIteratorPhalconPdoFetchAll implements IOfferIterator
{
    /** @var AdapterInterface */
    private $pdo;

    private $offers;

    public function __construct(AdapterInterface $pdo)
    {
        $this->pdo = $pdo;
    }

    public function current(): ?Offer
    {
        $data = current($this->offers);

        if ($data === false) {

            return null;
        }

        // TODO: checks

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
        return next($this->offers);
    }

    public function key()
    {
        return key($this->offers);
    }

    public function valid()
    {
        return $this->current() != null;
    }

    public function rewind()
    {
        $time = microtime(true);
        $this->offers = $this->pdo->fetchAll('SELECT id, available, price, url, category_id FROM offer ORDER BY url', Db::FETCH_ASSOC);
        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");
        $totalFetchedCategories = count($this->offers);
        print("Total fetched offers: {$totalFetchedCategories}\n");
    }
}
