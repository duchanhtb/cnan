<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');
/**
 * @author duchanh
 * @copyright 2012
 */
/* define admin folder 
  ---------------------------------------------------- */
define('ADMIN_FOLDER', 'admin');

/* config file
  ---------------------------------------------------- */
include('../config.php');

/* core admin cms
  ---------------------------------------------------- */
include_once('CmsTable.php');

/* admin function
  ---------------------------------------------------- */
include_once('admin.function.php');

/* define admin path
  ---------------------------------------------------- */
define('ADMIN_PATH', ROOT_PATH . DS . ADMIN_FOLDER . DS);

/* admin images uri
  ---------------------------------------------------- */
define('ADMIN_IMAGES', admin_url() . 'images/');

/* admin js uri
  ---------------------------------------------------- */
define('ADMIN_JS', admin_url() . 'js/');

/* admin css uri
  ---------------------------------------------------- */
define('ADMIN_CSS', admin_url() . 'css/');


/* config for left menu of admin
  $arrMenu[] = 	array(
  "name", 		'the name of menu'
  "id", 			'the php_file_name.php in "include" folder '
  "type", 		'if the value is heading, the 'id' unnecessary'
  "desc", 		'description for tooltip'
  )
  ---------------------------------------------------- */

$mt_prefix = 'm_';
$tbl_prefix = "t_";

/* Hướng dẫn sử dụng
  ---------------------------------------------------- */
$arrMenu[] = array("name" => 'Hướng dẫn',
    "type" => 'heading',
    "id" => '',
    'desc' => 'Tài liệu hướng dẫn sử dụng HCMS'
);
$arrMenu[] = array("name" => trans('how_to_use'),
    "id" => 'home',
    'desc' => 'Cách sử dụng hệ thống quản trị nội dung HCMS'
);


/* Công thức nấu ăn
  ---------------------------------------------------- */
$arrMenu[] = array("name" => 'Công thức',
    "type" => 'heading',
    "id" => '',
    'desc' => 'Quản lý công thức nấu ăn, chuyên mục, những bài viết người dùng upload lên'
);
$arrMenu[] = array("name" => 'Phân loại',
    "id" => 'recipe_category',
    "table" => $tbl_prefix . 'recipe_category:id',
    "desc" => 'Quản trị danh mục món ăn'
);
$arrMenu[] = array("name" => 'Công thức nấu ăn',
    "id" => 'recipe',
    "table" => $tbl_prefix . 'recipe:id',
    "desc" => 'Quản trị công thức món ăn, các thông tin liên quan'
);
$arrMenu[] = array("name" => 'Nhón nguyên liệu',
    "id" => 'ingridient_group',
    "table" => $mt_prefix . 'ingridient_group:id',
    "desc" => 'Quản trị nhón nguyên liệu'
);
$arrMenu[] = array("name" => 'Nguyên liệu',
    "id" => 'ingridient',
    "table" => $mt_prefix . 'ingridient:id',
    "desc" => 'Quản trị nguyên liệu nấu ăn'
);
$arrMenu[] = array("name" => 'Báo lỗi nội dung',
    "id" => 'recipe_report',
    "table" => $tbl_prefix . 'recipe_report:id',
    "desc" => 'Quản trị thông báo lỗi của người xem'
);


/* Địa điểm ăn ngon
  ---------------------------------------------------- */
$arrMenu[] = array("name" => 'Địa điểm ăn ngon',
    "type" => 'heading',
    "id" => '',
    'desc' => 'Quản lý địa điểm, những bài viết người dùng upload lên'
);
$arrMenu[] = array("name" => 'Địa điểm',
    "id" => 'dining_venues',
    "table" => $tbl_prefix . 'dining_venues:id',
    "desc" => 'Quản trị địa điểm ăn ngon, các thông tin liên quan'
);
$arrMenu[] = array("name" => 'Báo lỗi nội dung',
    "id" => 'dining_venues_report',
    "table" => $tbl_prefix . 'dining_venues_report:id',
    "desc" => 'Quản trị thông báo lỗi của người xem'
);


/* Blog ẩm thực
  ---------------------------------------------------- */
$arrMenu[] = array("name" => trans('Blog'),
    "type" => 'heading',
    "id" => '',
);
$arrMenu[] = array("name" => 'Phân loại',
    "id" => 'blog_category',
    "table" => $tbl_prefix . 'blog_category:id',
    "desc" => 'Quản trị danh mục blog'
);
$arrMenu[] = array("name" => 'Bài viết',
    "id" => 'blog',
    "table" => $tbl_prefix . 'blog:id',
    "desc" => 'Quản trị blog, bài viết'
);


/* Location
  ---------------------------------------------------- */
$arrMenu[] = array("name" => 'Khu vực',
    "type" => 'heading',
);
$arrMenu[] = array("name" => 'Tỉnh thành',
    "id" => 'province',
    "table" => $mt_prefix . 'provinces:id',
    "desc" => 'Quản lý tỉnh thành'
);
$arrMenu[] = array("name" => trans('district'),
    "id" => 'district',
    "table" => $mt_prefix . 'districts:id',
    "desc" => 'Quản lý quận huyện'
);


/* Các mục khác
  ---------------------------------------------------- */
$arrMenu[] = array("name" => trans('other_menu'),
    "type" => 'heading',
);
$arrMenu[] = array("name" => trans('language'),
    "id" => 'language',
    "table" => $mt_prefix . 'language:id',
    "desc" => 'Quản lý ngôn ngữ'
);
/*
$arrMenu[] = array("name" => trans('setting'),
    "id" => 'options',
    "desc" => 'Cài đặt các thông tin liên quan tới website như email, số đt, dường đẫn tới facebook...'
);
 * 
 */
$arrMenu[] = array("name" => trans('page'),
    "id" => 'page',
    "table" => $mt_prefix . 'page:id',
    "desc" => 'Quản trị các trang, Title và Keywords, Descriptions cho mỗi trang. Google và các máy tìm kiếm khác dựa vào những từ khóa này để đưa và hệ thống search engine của họ'
);
$arrMenu[] = array("name" => 'Media',
    "id" => 'media',
    "desc" => 'Quản trị ảnh đã uploaded lên server, dễ dàng thêm mới, sửa, xóa ảnh. Lưu ý khi xóa hoặc đổi tên thì ảnh ở ngoài trang chủ sẽ không hiện ra.'
);


/* Người dùng
  ---------------------------------------------------- */
$arrMenu[] = array("name" => 'Người dùng',
    "type" => 'heading',
    "id" => '',
    "desc" => 'Quản lý người dùng'
);
$arrMenu[] = array("name" => 'Admin',
    "id" => 'admin',
    "table" => $mt_prefix . 'admin:id',
    "desc" => 'Quản lý người dùng admin'
);
$arrMenu[] = array("name" => 'User',
    "id" => 'user',
    "table" => $tbl_prefix . 'user:id',
    "desc" => 'Quản lý người dùng thông thường'
);
$arrMenu[] = array("name" => 'Câu hỏi bí mật',
    "id" => 'security_questions',
    "table" => $mt_prefix . 'security_questions:id',
    "desc" => 'Quản lý câu hỏi bí mật
');
$arrMenu[] = array("name" => 'Nghề nghiệp',
    "id" => 'career',
    "table" => $mt_prefix . 'career:id',
    "desc" => 'Quản lý nghề nghiệp người dùng'
);
$arrMenu[] = array("name" => 'Điều khoản sử dụng',
    "id" => 'policy',
    "table" => $mt_prefix . 'policy:id',
    "desc" => 'Quản lý nội dung policy'
);


$admin_title = getAdminTitle($arrMenu);
