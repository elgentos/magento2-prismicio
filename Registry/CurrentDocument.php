<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 10-4-19
 * Time: 22:05
 */

namespace Elgentos\PrismicIO\Registry;

class CurrentDocument
{
    private $document;

    public function setDocument(\stdClass $document): void
    {
        $this->document = $document;
    }

    public function getDocument(): ?\stdClass
    {
        return $this->document;
    }
}
