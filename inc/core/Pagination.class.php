<?php

/**
 * @author duchanh
 * @copyright 2012
 */
class Page {

    var $indexPage = 1;
    var $totalPages = 1;
    var $totalRows = 0;
    var $rowsView;
    var $limitStart;
    var $strSubmit;
    var $pageToLink;
    var $ortherSubmit;

    function Page($totalRows, $indexPage, $rows, $strSubmit, $pageToLink, $ortherSubmit) {
        $this->indexPage = $indexPage;
        $this->totalRows = $totalRows;
        $this->rowsView = $rows;
        $this->strSubmit = $strSubmit;
        $this->pageToLink = $pageToLink;
        $this->ortherSubmit = $ortherSubmit;
        $numRows = $this->totalRows;
        if ($numRows % $rows == 0) {
            $this->totalPages = (int) ($numRows / $rows);
        } else {
            $this->totalPages = (int) ($numRows / $rows) + 1;
        }
        $indexStart = ($this->indexPage) * $rows - $rows;
        $this->limitStart = $indexStart;
    }

    function getRowsView() {
        return $this->rowsView;
    }

    function getLimitStart() {
        return $this->limitStart;
    }

    function getLimitEnd() {
        return $this->limitEnd;
    }

    function getTotalPages() {
        return $this->totalPages;
    }

    function setIndexPage($index) {
        $this->indexPage = $index;
    }

    function getIndexPage() {
        return $this->indexPage;
    }

    function getTotalCols($number_col_display) {
        $totalCols = 0;
        if ($this->getTotalPages() % $number_col_display == 0) {
            $totalCols = (int) ($this->getTotalPages() / $number_col_display);
        } else {
            $totalCols = (int) ($this->getTotalPages() / $number_col_display) + 1;
        }
        return $totalCols;
    }

    function getCurrentCol($number_col_display) {
        $currentCol = 0;
        if ($this->getIndexPage() % $number_col_display == 0) {
            $currentCol = (int) ($this->getIndexPage() / $number_col_display);
        } else {
            $currentCol = (int) ($this->getIndexPage() / $number_col_display) + 1;
        }
        return $currentCol;
    }

    function getPageToLink() {
        return $this->pageToLink;
    }

    function getStrSubmit() {
        return $this->strSubmit;
    }

    function displayStringTotalPage($number_col_display) {
        $totalCols = $this->getTotalCols($number_col_display);
        $currentCol = $this->getCurrentCol($number_col_display);
        if ($this->getIndexPage() == $currentCol * $number_col_display && $this->getIndexPage() < $this->getTotalPages()) {
            $currentCol = $currentCol + 1;
        }
        $sstar = ($currentCol - 1) * $number_col_display;
        $eend = $sstar + $number_col_display;
        if ($eend > $this->getTotalPages()) {
            $eend = $sstar + $this->getTotalPages() % $number_col_display;
        }
        for ($i = $sstar; $i < $eend; $i++) {
            echo " <a href='" . $this->getPageToLink() . "&" . $this->getStrSubmit() . "=" . ($i + 1) . "&" . $this->ortherSubmit . "'>" . ($i + 1) . "</a> ";
        }
    }

    function displayPriview() {
        if ($this->getIndexPage() > 1) {
            echo " <a href='" . $this->getPageToLink() . "&" . $this->getStrSubmit() . "=" . ($this->getIndexPage() - 1) . "&" . $this->ortherSubmit . "'>Priview</a> | ";
        }
    }

    function displayNext() {
        if ($this->getIndexPage() < $this->getTotalPages()) {
            echo " <a href='" . $this->getPageToLink() . "&" . $this->getStrSubmit() . "=" . ($this->getIndexPage() + 1) . "&" . $this->ortherSubmit . "'> | Next</a>";
        }
    }

    function displayArrayPages($numberCol) {
        $this->displayStringTotalPage($numberCol);
    }
}