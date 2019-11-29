<?php

namespace Yml;

use XMLWriter;
use Yml\Models\Category;
use Yml\Models\ICategoryIterator;
use Yml\Models\IOfferIterator;
use Yml\Models\Offer;

// TODO: inject LoggerInterface instead of print (remove side-affects)
class Generator
{
    /** @var XMLWriter */
    private $xmlWriter;


    public function __construct(XMLWriter $xmlWriter)
    {
        $this->xmlWriter = $xmlWriter;
    }

    public function generate(
        string $shopName,
        string $companyName,
        string $url,
        ICategoryIterator $categories,
        IOfferIterator $offers
    ) {

        $this->addHeader($shopName, $companyName, $url);
        $this->addCategories($categories);
        $this->addOffers($offers);
        $this->addFooter();
    }

    private function addHeader(
        string $shopName,
        string $companyName,
        string $url
    ) {
        $this->xmlWriter->startDocument('1.0', "UTF-8");
        $this->xmlWriter->startDTD('yml_catalog', null, 'shops.dtd');
        $this->xmlWriter->endDTD();
        $this->xmlWriter->startElement('yml_catalog');
        $this->xmlWriter->writeAttribute('date', date('Y-m-d H:i'));
        $this->xmlWriter->startElement('shop');

        $this->xmlWriter->writeElement('name', $shopName);
        $this->xmlWriter->writeElement('company', $companyName);
        $this->xmlWriter->writeElement('url', $url);

        $this->xmlWriter->startElement('currencies');
        $this->xmlWriter->startElement('currency');
        $this->xmlWriter->writeAttribute('id', "RUB");
        $this->xmlWriter->writeAttribute('rate', "1");
        $this->xmlWriter->endElement();
        $this->xmlWriter->fullEndElement();
    }

    private function addFooter()
    {
        $this->xmlWriter->fullEndElement();
        $this->xmlWriter->fullEndElement();
        $this->xmlWriter->endDocument();
    }

    private function addCategories(ICategoryIterator $categories)
    {
        $this->xmlWriter->startElement('categories');
        $i = 0;
        print "Generating categories:\n";
        foreach ($categories as $category) {
            $this->addCategory($category);
            print("\r" . ++$i . ' objects');
        }
        print "\n";
        $this->xmlWriter->fullEndElement();
    }

    private function addCategory(Category $category)
    {
        $this->xmlWriter->startElement('category');
        $this->xmlWriter->writeAttribute('id', $category->id());
        if ($category->parentId() !== null) {
            $this->xmlWriter->writeAttribute('parentId', $category->parentId());
        }
        $this->xmlWriter->text($category->name());
        $this->xmlWriter->fullEndElement();

    }

    private function addOffers(IOfferIterator $offers)
    {
        $this->xmlWriter->startElement('offers');
        $i = 0;
        print "Generating offers:\n";
        foreach ($offers as $offer) {
            $this->addOffer($offer);
            print("\r" . ++$i . ' objects');
        }
        print "\n";
        $this->xmlWriter->fullEndElement();
    }

    private function addOffer(Offer $offer)
    {
        $this->xmlWriter->startElement('offer');
        $this->xmlWriter->writeAttribute('id', $offer->id());
        $this->xmlWriter->writeAttribute('available', $offer->available());
        $this->xmlWriter->writeElement('price', $offer->price());
        $this->xmlWriter->writeElement('url', $offer->url());
        $this->xmlWriter->writeElement('categoryId', $offer->categoryId());
        $this->xmlWriter->fullEndElement();
    }
}
