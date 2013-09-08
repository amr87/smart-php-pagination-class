<?php
/*
Pagination Class V 2.0
Developed By Amr Gamal
Date: 3rd August 2013
License: MIT
*/

class Pagination
{
    
    private $rows;
    public $perPage;
    public $sql;
    public $numLinks;
    private $lastPage;
    private $output;
    private $buttonsText;
    private $currentPage;
    private $limit;
    private $url;
    private $boundaries;
    private $urlParams;
    private $showStatus ;
    private $showJumpToPage;
    
    
    public function __construct($config)
    {
        
        $this->sql            = @$config["sql"];
        $this->urlParams      = @$config["urlParams"];
        $this->buttonsText    = @$config["buttonsText"];
        $this->perPage        = isset($config["perPage"]) ? $config["perPage"] : 10;
        $this->numLinks       = isset($config["numLinks"]) ? $config["numLinks"] : 7;
        $this->showJumpToPage = isset($config["showJumpToPage"]) ? $config["showJumpToPage"] : false;
        $this->showStatus     = isset($config["showStatus"]) ? $config["showStatus"]  : false;
        $this->boundaries     = ($this->numLinks % 2 == 0) ? array(
            $this->numLinks / 2,
            ($this->numLinks / 2) - 1
        ) : array(
            ($this->numLinks / 2) - 1,
            ceil($this->numLinks / 2) - 1
        );
        $this->rows           = $this->totalRows();
        $this->lastPage       = $this->getLastPage();
        $this->output         = "";
        $this->currentPage    = isset($_GET['page']) ? preg_replace("#[^0-9]#", "", $_GET['page']) : 1;
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        } elseif ($this->currentPage > $this->lastPage) {
            $this->currentPage = $this->lastPage;
        }
        
        $this->limit = " LIMIT " . ($this->currentPage - 1) * $this->perPage . "," . $this->perPage;
        $this->sql .= " " . $this->limit;
        $this->url = rtrim(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '?');
        
    }
    
    private function totalRows()
    {
        return (int) mysql_num_rows(mysql_query($this->sql));
    }
    
    private function getLastPage()
    {
        $last = ceil($this->rows / $this->perPage);
        return ($last < 1) ? 1 : $last;
    }
    
    public function getObjects()
    {
        $objectsArray = array();
        $query        = mysql_query($this->sql);
        while ($row = mysql_fetch_object($query)) {
            $objectsArray[] = $row;
        }
        return $objectsArray;
    }
    
    
    public function nav()
    {
        
        if ($this->lastPage != 1) {
            
            if ($this->currentPage == 1) {
                for ($i = 0; $i <= $this->numLinks; $i++) {
                    if ($i == 1) {
                        $this->output .= "<a class='link active'>" . $this->currentPage . "</a>\n";
                    } elseif ($i > 0 && $i <= $this->lastPage) {
                        $this->output .= "<a class='link' href='" . $this->url . "?page=" . $i . "&" . $this->urlParams . "'>" . $i . "</a>\n";
                    }
                }
                
            }
            
            if ($this->currentPage == $this->lastPage) {
                $previous = $this->lastPage - 1;
                // output previous link
                $this->output .= "<a class='link first' title= 'go to first page' href='" . $this->url . "?page=1&" . $this->urlParams . "'>";
                $this->output .= isset($this->buttonsText["first"]) ? $this->buttonsText["first"] . "</a>\n" : "First</a>\n";
                $this->output .= "<a class='link prev' title= 'go to previous page' href='" . $this->url . "?page=" . $previous . "&" . $this->urlParams . "'>";
                $this->output .= isset($this->buttonsText["previous"]) ? $this->buttonsText["previous"] . "</a>\n" : "Previous</a>\n";
                for ($i = $this->lastPage - $this->numLinks + 1; $i <= $this->lastPage; $i++) {
                    if ($i == $this->lastPage) {
                        $this->output .= "<a class='link active'>" . $this->currentPage . "</a>\n";
                    } elseif ($i > 0) {
                        $this->output .= "<a class='link' href='" . $this->url . "?page=" . $i . "&" . $this->urlParams . "'>" . $i . "</a>\n";
                    }
                }
                
            }
            
            //check to see if we are on page 1
            if ($this->currentPage > 1 && $this->currentPage != $this->lastPage) {
                $previous = $this->currentPage - 1;
                // output previous link
                $this->output .= "<a class='link first' title= 'go to first page' href='" . $this->url . "?page=1&" . $this->urlParams . "'>";
                $this->output .= isset($this->buttonsText["first"]) ? $this->buttonsText["first"] . "</a>\n" : "First</a>\n";
                $this->output .= "<a class='link prev' title= 'go to previous page' href='" . $this->url . "?page=" . $previous . "&" . $this->urlParams . "'>";
                $this->output .= isset($this->buttonsText["previous"]) ? $this->buttonsText["previous"] . "</a>\n" : "Previous</a>\n";
                // output left side links
                for ($i = $this->currentPage - $this->boundaries[1]; $i < $this->currentPage; $i++) {
                    if ($i > 0) {
                        $this->output .= "<a class='link' href='" . $this->url . "?page=" . $i . "&" . $this->urlParams . "'>" . $i . "</a>\n";
                    }
                }
            }
           if ($this->currentPage != 1 && $this->currentPage != $this->lastPage) {
                // output the current page as inactive link
                $this->output .= "<a class='link active'>" . $this->currentPage . "</a>\n";
                
                // output left right links
                for ($i = $this->currentPage + 1; $i <= $this->lastPage; $i++) {
                    $this->output .= "<a class='link' href='" . $this->url . "?page=" . $i . "&" . $this->urlParams . "'>" . $i . "</a>\n";
                    if ($i >= $this->currentPage + $this->boundaries[0]) {
                        break;
                    }
                }
            }
            
            // output next link
            if ($this->currentPage != $this->lastPage) {
                $next = $this->currentPage + 1;
                $this->output .= "<a class='link next' title= 'go to next page' href='" . $this->url . "?page=" . $next . "&" . $this->urlParams . "'>";
                $this->output .= isset($this->buttonsText["next"]) ? $this->buttonsText["next"] . "</a>\n" : "Next</a>\n";
                $this->output .= "<a class='link last' title= 'go to last page' href='" . $this->url . "?page=" . $this->lastPage . "&" . $this->urlParams . "'>";
                $this->output .= isset($this->buttonsText["last"]) ? $this->buttonsText["last"] . "</a>\n" : "Last</a>\n";
            }
            if ($this->showStatus) {
                $this->output .= "<a class='link status'>Page " . $this->currentPage . " of " . $this->lastPage . "</a>\n";
            }
            if ($this->showJumpToPage) {
                $this->output .= "<label><select onchange=\"javascript:window.location='" . $this->url . "?page='+this.selectedIndex" . $this->urlParams . "\">\n";
                $this->output .= "<option>Jump to Page</option>\n";
                for ($i = 1; $i <= $this->lastPage; $i++) {
                    $this->output .= "<option value='" . $i . "'>" . $i . "</option>\n";
                }
                $this->output .= "</select></label>";
            }
            
        }
        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', preg_replace("#&'>#", "'>", $this->output));
    }
    
}

?>
