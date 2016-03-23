<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 * @desc class about news
 */
class Blog extends Base {

    var $fields = array(
        'cat_id',
        'title',
        'brief',
        'content',
        'user_id',
        'username',
        'date_created',
        'date_updated',
        'img',
        'hits',
        'home',
        'status',
        'ordering',
        'meta_title',
        'meta_keyword',
        'meta_description'
    ); //fields in table (excluding Primary Key)	
    var $table = "t_blog";

    /**
     * @Desc get news by category
     * @param int $cat_id: category id
     * @param string $sort: sort field
     * @param string $order: DESC or ASC
     * @param int $page: paging
     * @return array
     */
    function getBlogByCategory($cat_id, $sort = 'ordering', $order = 'DESC', $page = 1) {
        global $oDb;
        $page = ($page > 1) ? $page : 1;
        $num_per_page = ($this->num_per_page > 0 ) ? $this->num_per_page : 20;
        $start = ($page - 1) * ($this->num_per_page);

        $sql = " SELECT * 
                    FROM $this->table
                    WHERE 1 AND status = 1 AND FIND_IN_SET( '$cat_id' , `cat_id` )";

        $sql .= " ORDER BY $sort $order ";
        $sql .= " LIMIT $start, $num_per_page ";

        $rc = $oDb->query($sql);
        $rs = $oDb->fetchAll($rc);
        return $rs;
    }

    /**
     * @Desc get news by condition
     * @param string $con: the query condition
     * @param string $sort: sort field
     * @param string $order: direction, ASC or DESC 
     * @param int $page: page number
     * @return array
     */
    function getBlog($con = "", $sort = 'ordering', $order = 'DESC', $page = 1) {
        $page = ($page > 1) ? $page : 1;
        $num_per_page = ($this->num_per_page > 0 ) ? $this->num_per_page : 20;
        $start = ($page - 1) * ($this->num_per_page);

        $list_field = '*';
        return $this->get($list_field, $con, " $sort $order", $start, $num_per_page);
    }

    /**
     * @Desc update hits news
     * @param int $id: id of the table
     * @return boolean
     */
    function hits($id) {
        global $oDb;
        $sql = " UPDATE `$this->table` SET `hits` = `hits` + 1 WHERE `$this->table`.`id` = $id LIMIT 1 ";
        $oDb->query($sql);
        return true;
    }

    /**
     * @Desc get other news
     * @param int $cat_id: category id
     * @param int $cur_id: curent id
     * @param int $number: number want to get     
     * @return array
     */
    function getOtherBlog($cat_id, $cur_id, $get_number = 5) {
        $Category = new Category();
        $allNewsCategory = $Category->getCatByType(1);
        $list_id = $Category->getListSubCategory($allNewsCategory, $cat_id);
        $con = " AND cat_id IN ($list_id) AND id != $cur_id";
        return $this->get('*', $con, 'ordering DESC', 0, $number);
    }

}
