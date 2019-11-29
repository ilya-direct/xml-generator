<?php

namespace Yml;

use XMLWriter;
use Yml\Models\ICategoryIterator;
use Yml\Models\IOfferIterator;

class Wrapper
{
    private $xmlWriter;
    private $fileName;

    public function __construct(
        string $fileName
    )
    {
        $this->xmlWriter = new XMLWriter();
        $this->fileName = $fileName;
    }

    public function generate(
        string $shopName,
        string $companyName,
        string $url,
        ICategoryIterator $categories,
        IOfferIterator $offers
    )
    {
        $tempFile = \tempnam(\sys_get_temp_dir(), 'price');

        // Write to temp file
        $this->xmlWriter->openUri($tempFile);

        $generator = new Generator($this->xmlWriter);
        $generator->generate(
            $shopName,
            $companyName,
            $url,
            $categories,
            $offers,
         );

        $this->xmlWriter->flush();
        
        // copy to real file after generating
        \copy($tempFile, $this->fileName);
        @\unlink($tempFile);
    }
}
