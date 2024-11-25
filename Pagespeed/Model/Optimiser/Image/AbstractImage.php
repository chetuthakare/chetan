<?php
namespace Swissup\Pagespeed\Model\Optimiser\Image;

use Swissup\Pagespeed\Helper\Config;
use Swissup\Pagespeed\Model\Optimiser\AbstractCachableOptimiser;

abstract class AbstractImage extends AbstractCachableOptimiser
{
    /**
     *
     * @var \Laminas\Dom\Query
     */
    private $dom;

    /**
     *
     * @return \Laminas\Dom\Query
     */
    protected function getDom()
    {
        if ($this->dom === null) {
            $this->dom = new \Laminas\Dom\Query('');
        }

        return $this->dom;
    }

    /**
     *
     * @param  string $html
     * @return array
     */
    protected function getImagesFromHtml($html)
    {
        $_html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $images = [];
        preg_match_all('/<img[\s\r\n]+.*?>/is', (string) $_html, $images);
        unset($_html);
        $images = isset($images[0]) ? $images[0] : [];

        return $images;
    }

    /**
     *
     * @param  string $imageHTML
     * @return \DOMNode
     */
    protected function getDOMNodeFromImageHtml($imageHTML)
    {
        $dom = $this->getDom();

        $encoding = mb_detect_encoding($imageHTML, 'auto');
        if ($encoding === 'UTF-8') {
            $imageHTML = "\xEF\xBB\xBF" . $imageHTML;
        }
        if ($encoding) {
            $dom->setDocumentHtml($imageHTML, $encoding);
        } else {
            $dom->setDocumentHtml($imageHTML);
        }
        $node = $dom->execute('img')->current();

        return $node;
    }

    /**
     *
     * @param  \DOMNode $node
     * @return string
     */
    protected function getImageHtmlFromDOMNode($node)
    {
        // $imageHtml = $node->C14N();
        $clonedNode = $node->cloneNode(true);
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        // silence warnings and erros during parsing.
        // More at http://php.net/manual/en/function.libxml-use-internal-errors.php
        $oldUseErrors = libxml_use_internal_errors(true);
        $dom->appendChild($dom->importNode($clonedNode, true));
        $imageHtml = $dom->saveHTML();
        // restore old value
        libxml_use_internal_errors($oldUseErrors);
        $imageHtml = mb_convert_encoding($imageHtml, 'ISO-8859-1', 'UTF-8');

        $imageHtml = html_entity_decode($imageHtml);
        // $imageHtml = $this->getUtf8ToHtml($imageHtml);
        $imageHtml = str_replace('></img>', ' />', $imageHtml);

        return $imageHtml;
    }

    /**
     *
     * @param  string $html
     * @param  string $imageHTML
     * @return bool
     */
    protected function isParentTagPicture($html, $imageHTML)
    {
        $position = strpos($html, (string) $imageHTML);
        $subHtml = substr($html, $position, 500);
        $length = strpos($subHtml, '</');
        $subHtml = substr($subHtml, $length);
        $length = strpos($subHtml, '>');
        $parentTag = substr($subHtml, 2, $length - 2);

        return $parentTag === 'picture' ;
    }
}
