<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class SimpleXMLExtended extends SimpleXMLElement {

    /**
     * add CDATA function
     *
     * @param unknown_type $cdata_text
     */
    public function addCData($cdata_text) {

        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }
}