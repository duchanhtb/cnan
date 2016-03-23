<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');
/**
 * @author duchanh
 * @copyright 2012
 */
global $of_options;
define('OPTIONS_IMAGES', ADMIN_IMAGES . 'options/');


/* ----------------------------------------------------------------------------------- */
/* General */
admin_register_style('cms-option', admin_url() . 'css/admin-options.css');
admin_register_style('cms-option-colorpicker', admin_url() . 'css/color-picker.min.css');
admin_register_style('cms-option-jquery-ui', admin_url() . 'css/jquery-ui-custom.css');
admin_register_style('cms-option-color-pick', admin_url() . 'css/colpick.css');
admin_register_style('cms-option-stickytooltip', admin_url() . 'css/stickytooltip.css');
admin_register_style('cms-option-stickytooltip', admin_url() . 'css/stickytooltip.css');

/* ----------------------------------------------------------------------------------- */
$of_options[] = array("name" => trans('General'),
    "type" => 'heading',
    "icon" => OPTIONS_IMAGES . "icon-settings.png"
);

$of_options[] = array('name' => trans('General'),
    'type' => 'info',
    'std' => trans('General'),
);

$of_options[] = array('name' => trans('Disable onepage mode?'),
    'type' => 'switch',
    'std' => false,
    'id' => 'disable-onepage',
    'desc' => trans('Turn it ON to disable <strong>Onepage</strong> mode and turn into <strong>Multi-pages</strong>.'),
);

$of_options[] = array('name' => trans('Enable responsive layout?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'responsive',
    'desc' => trans('If turned OFF, your site on mobile devices will look the same as on the desktop screen'),
);

$of_options[] = array('name' => trans('Enable Scroll to top button?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'scrollup',
    'desc' => trans('Scroll top button is the small square button at the bottom right.'),
);

$of_options[] = array('name' => trans('Enable theme colorbox?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'colorbox',
    'desc' => trans('Norris has included Colorbox plugin. Turn it OFF if you don\'t want to use it or you want to use your own plugin.'),
);

$of_options[] = array('name' => trans('Header Code'),
    'type' => 'textarea',
    'std' => '',
    'id' => 'header-code',
    'desc' => trans('You can load Google fonts here.'),
);

$of_options[] = array('name' => trans('Footer Code'),
    'type' => 'textarea',
    'std' => '',
    'id' => 'footer-code',
    'desc' => trans('You can Google Analytics tracking code or something here.'),
);

$of_options[] = array('name' => trans('Custom CSS'),
    'type' => 'textarea',
    'std' => '',
    'id' => 'custom-css',
    'desc' => trans('If you know a little about CSS, you can write your custom CSS here. Do not edit CSS files (will be lost when you update this theme).'),
);

/* Icons */
$of_options[] = array('name' => trans('Icons'),
    'type' => 'info',
    'std' => trans('Icons'),
);

$of_options[] = array('name' => trans('Favicon'),
    'type' => 'media',
    'id' => 'favicon',
    'desc' => trans('Favicon is a small icon image at the topbar of your browser. Should be 16x16px or 32x32px image (png, ico...)'),
);

$of_options[] = array('name' => trans('Apple iPhone icon (57x57px)'),
    'type' => 'media',
    'id' => 'apple-iphone-icon',
    'desc' => trans('Similar to the Favicon, the <strong>Apple iPhone icon</strong> is a file used for a web page icon on the Apple iPhone. When someone bookmarks your web page or adds your web page to their home screen this icon is used. If this file is not found these Apple products will use the screen shot of the web page, which often looks like no more than a white square.'),
);

$of_options[] = array('name' => trans('Apple iPhone retina icon (114x114px)'),
    'type' => 'media',
    'id' => 'apple-iphone-retina-icon',
    'desc' => trans('The same as Apple iPhone icon but for Retina iPhone.'),
);

$of_options[] = array('name' => trans('Apple iPad icon (72x72px)'),
    'type' => 'media',
    'id' => 'apple-ipad-icon',
    'desc' => trans('The same as Apple iPhone icon but for iPad.'),
);

$of_options[] = array('name' => trans('Apple iPad Retina icon (144x144px)'),
    'type' => 'media',
    'id' => 'apple-ipad-retina-icon',
    'desc' => trans('The same as Apple iPhone icon but for Retina iPad.'),
);

/* ----------------------------------------------------------------------------------- */
/* Toparea
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Top area'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "icon-home.png",
);

$of_options[] = array('name' => trans('Top area content'),
    'type' => 'textarea',
    'std' => '[toparea_small]Founded February 20, 2013[/toparea_small]
										[toparea_big]Withemes[/toparea_big]
										[toparea_small]We create elegant and clean Wordpress themes[/toparea_small]
										[spacer]
										[button color="none" scroll="true" icon="angle-right" icon_position="right" link="#about-us"]Get Started[/button]',
    'id' => 'top-area-content',
    'desc' => trans('You can use both shortcodes and HTML for this field.'),
);

$of_options[] = array('name' => trans('Top area type'),
    'type' => 'select',
    'std' => 'bg-fullscreen',
    'id' => 'top-area-type',
    'options' => array(
        'bg-fullscreen' => trans('Fullscreen background image'),
        'bg-not-fullscreen' => trans('Not fullscreen background image'),
        'slider-fullscreen' => trans('Fullscreen background slideshow'),
        'slider-not-fullscreen' => trans('Not fullscreen background slideshow'),
        'fullwidth-content' => trans('Fullwidth Content'),
        'none' => trans('Do not display top area.'),
    ),
    'desc' => trans('Select a type and scroll to respective option area below.'),
);

$of_options[] = array('name' => trans('Background image'),
    'type' => 'media',
    'std' => '/images/wooden.jpg',
    'id' => 'toparea-bg-background-image',
    'desc' => trans('Any size works well on Chrome/Firefox/Safari but at least 1366px in width to work on IE8.'),
);

$of_options[] = array('name' => trans('Padding top & bottom (px)'),
    'type' => 'sliderui',
    'id' => 'toparea-bg-padding-top-bottom',
    'std' => 100,
    'min' => 10,
    'max' => 400,
    'step' => 10,
    'desc' => trans('This option is used for not-fullscreen mode.'),
);

$of_options[] = array('name' => trans('Slides'),
    'type' => 'slider',
    'std' => '',
    'id' => 'toparea-slider-slider',
);

$of_options[] = array('name' => trans('Slideshow effect'),
    'type' => 'select',
    'std' => 'slideRight',
    'options' => array(
        'fade' => trans('Fade'),
        'slideTop' => trans('Slide top'),
        'slideBottom' => trans('Slide bottom'),
        'slideRight' => trans('Slide right'),
        'slideLeft' => trans('Slide left'),
        'carouselRight' => trans('Carousel right'),
        'carouselLeft' => trans('Carousel left'),
        'none' => trans('None'),
    ),
    'id' => 'toparea-slider-fullscreen-effect',
    'desc' => trans('This option is used only for fullscreen slideshow.'),
);

$of_options[] = array('name' => trans('Not fullscreen slideshow effect'),
    'type' => 'select',
    'std' => 'slide',
    'options' => array(
        'fade' => trans('Fade'),
        'slide' => trans('Slide'),
    ),
    'id' => 'toparea-slider-not-fullscreen-effect',
    'desc' => trans('This option is used only for not-fullscreen slideshow.'),
);

$of_options[] = array('name' => trans('Parallax Effect?'),
    'type' => 'switch',
    'std' => false,
    'id' => 'top-area-parallax',
    'desc' => trans('Enable parallax effect for top area'),
);

$of_options[] = array('name' => trans('Overlay opacity'),
    'type' => 'sliderui',
    'id' => 'toparea-bg-overlay-opacity',
    'std' => 60,
    'min' => 1,
    'max' => 100,
    'step' => 1,
    'desc' => trans('The bigger number is, the darker background should be.'),
);

$of_options[] = array('name' => trans('Auto play?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'toparea-slider-auto',
);

$of_options[] = array('name' => trans('Time between 2 slides (in seconds)'),
    'type' => 'sliderui',
    'std' => 5,
    'min' => 1,
    'max' => 20,
    'step' => 1,
    'id' => 'toparea-slider-time-interval',
    'desc' => trans('The time interval between 2 slides (if auto play is enabled).'),
);

$of_options[] = array('name' => trans('Show navigation?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'toparea-slider-navi',
);

$of_options[] = array('name' => trans('Show pager?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'toparea-slider-pager',
);


$of_options[] = array('name' => trans('Show progress bar for fullscreen slideshow?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'toparea-slider-fullscreen-progress',
);

/* ----------------------------------------------------------------------------------- */
/* Header
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Header'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "header.png",
);

/* Header */
$of_options[] = array('name' => trans('Header'),
    'type' => 'info',
    'std' => trans('Header'),
);

$of_options[] = array('name' => trans('Header layout'),
    'type' => 'select',
    'std' => 'span3',
    'id' => 'header-proportion',
    'options' => array(
        'span2' => trans('2:10'),
        'span3' => trans('3:9'),
        'span4' => trans('4:8'),
        'span5' => trans('5:7'),
        'span6' => trans('6:6'),
    ),
    'desc' => trans('These proportions are proportion between logo area and navigation area. If you have a big logo, you should choose 4:8 or 5:7 for example.'),
);

$of_options[] = array('name' => trans('Header always stick?'),
    'type' => 'switch',
    'std' => false,
    'id' => 'header-always-stick',
    'desc' => trans('Turn it ON to make header is always stick at the top, on all pages (by default, it\'s stuck only on Onepage for navigation purpose).'),
);

$of_options[] = array('name' => trans('Header at very top?'),
    'type' => 'switch',
    'std' => false,
    'id' => 'header-at-top',
    'desc' => trans('Turn it ON if you want to display header at very top of your site.'),
);

$of_options[] = array('name' => trans('Header top black line?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'header-top-black-line',
    'desc' => trans('Turn it OFF if you do not want to show header top black line.'),
);

/* Logo */
$of_options[] = array('name' => trans('Logo'),
    'type' => 'info',
    'std' => trans('Logo'),
);



$of_options[] = array('name' => trans('Use text logo'),
    'type' => 'switch',
    'std' => false,
    'id' => 'text-logo',
    'desc' => trans('Turn it ON if you want to use text logo instead of image logo.'),
);

$of_options[] = array('name' => trans('Logo'),
    'type' => 'media',
    'id' => 'logo',
    'std' => '/images/logo.png',
    'desc' => trans('The best size is 200x50px'),
);

$of_options[] = array('name' => trans('Retina logo'),
    'type' => 'media',
    'id' => 'retina-logo',
    'std' => '/images/logo@2x.png',
    'desc' => trans('2x times your logo dimension.'),
);

$of_options[] = array('name' => trans('Logo width (px)'),
    'type' => 'text',
    'id' => 'logo-width',
    'std' => '122',
    'desc' => trans('Width of your normal logo (not Retina). If you leave this field blank, the Retina logo won\'t work.'),
);

$of_options[] = array('name' => trans('Logo height (px)'),
    'type' => 'text',
    'id' => 'logo-height',
    'std' => '37',
    'desc' => trans('Height of your normal logo (not Retina). If you leave this field blank, the Retina logo won\'t work.'),
);

$of_options[] = array('name' => trans('Logo margin top (px)'),
    'type' => 'sliderui',
    'id' => 'logo-margin-top',
    'min' => 0,
    'max' => 50,
    'step' => 1,
    'std' => 10,
);

$of_options[] = array('name' => trans('Logo margin left (px)'),
    'type' => 'sliderui',
    'id' => 'logo-margin-left',
    'min' => 0,
    'max' => 200,
    'step' => 1,
    'std' => 0,
);

/* Header style */
$of_options[] = array('name' => trans('Header theme'),
    'type' => 'info',
    'std' => trans('Header theme'),
);

$of_options[] = array('name' => trans('Header theme'),
    'type' => 'select',
    'options' => array('light' => trans('Light'), 'dark' => trans('Dark'),),
    'id' => 'header-theme',
    'std' => 'light',
);

/* ----------------------------------------------------------------------------------- */
/* Footer
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Footer'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "footer.png"
);

$of_options[] = array('name' => trans('Footer text'),
    'type' => 'textarea',
    'id' => 'footer-text',
    'std' => '&copy; 2013. All rights reserved. Designed by <a href="http://withemes.com" target="_blank">WiThemes</a>',
    'desc' => trans('HTML is allowed.'),
);

$of_options[] = array('name' => trans('Show social icons on footer'),
    'type' => 'switch',
    'id' => 'footer-social',
    'std' => true,
    'desc' => trans('If turned ON, social icons will be taken from the social option area.'),
);

$of_options[] = array('name' => trans('Social icon link target?'),
    'type' => 'select',
    'id' => 'footer-social-target',
    'std' => '_blank',
    'options' => array('_blank' => trans('New Tab'), '_self' => trans('Current Tab')),
    'desc' => trans('Open social links in current tab/window or new tab/window?'),
);

$of_options[] = array('name' => trans('Display social icon title'),
    'type' => 'switch',
    'id' => 'footer-social-title',
    'std' => true,
    'desc' => trans('Title is a small text appears when you hover the icon'),
);

$of_options[] = array('name' => trans('Footer logo'),
    'type' => 'media',
    'id' => 'footer-logo',
    'std' => '/images/footer-logo.png',
    'desc' => trans('The best size should be 100x27px'),
);

$of_options[] = array('name' => trans('Retina footer logo'),
    'type' => 'media',
    'id' => 'retina-footer-logo',
    'std' => '/images/footer-logo@2x.png',
    'desc' => trans('2x times your footer logo dimension.'),
);

$of_options[] = array('name' => trans('Footer logo width (px)'),
    'type' => 'text',
    'id' => 'footer-logo-width',
    'std' => '161',
    'desc' => trans('Width of your normal footer logo (not Retina). If you leave this field blank, the Retina footer logo won\'t work.'),
);

$of_options[] = array('name' => trans('Footer logo height (px)'),
    'type' => 'text',
    'id' => 'footer-logo-height',
    'std' => '49',
    'desc' => trans('Height of your normal footer logo (not Retina). If you leave this field blank, the Retina footer logo won\'t work.'),
);

$bg_images_path = ADMIN_PATH . '/images/sidrbg/'; // change this to where you store your bg images
$bg_images_url = ADMIN_IMAGES . '/sidrbg/'; // change this to where you store your bg images
$bg_images = array();
if (is_dir($bg_images_path)) {
    if ($bg_images_dir = opendir($bg_images_path)) {
        while (($bg_images_file = readdir($bg_images_dir)) !== false) {
            if ((stristr($bg_images_file, ".png") !== false || stristr($bg_images_file, ".jpg") !== false || stristr($bg_images_file, ".gif") !== false ) && stristr($bg_images_file, '2X') === false) {
                $bg_images[] = $bg_images_url . $bg_images_file;
            }
        }
    }
}

$of_options[] = array("name" => trans('Footer background pattern'),
    "desc" => trans('Select footer background pattern'),
    "id" => "footerbg",
    "type" => "tiles",
    "options" => $bg_images,
);

$of_options[] = array("name" => trans('Footer custom background pattern'),
    "desc" => trans('Upload your own footer background pattern'),
    "id" => "footer-custom-bg",
    "type" => "media",
);

$of_options[] = array("name" => trans('Footer custom background pattern (Retina @2x version)'),
    "desc" => trans('Upload your own footer background pattern with double sizes.'),
    "id" => "footer-custom-bg-retina",
    "type" => "media",
);

$of_options[] = array("name" => trans('Use background color instead of patterns?'),
    "desc" => trans('Turn it ON if you want to use color instead of patterns.'),
    "id" => "use-footer-background-color",
    "type" => "switch",
);

$of_options[] = array("name" => trans('Footer background color'),
    "desc" => trans('Turn it ON if you want to use color instead of patterns.'),
    "id" => "footer-background-color",
    "type" => "color",
);

/* ----------------------------------------------------------------------------------- */
/* Styling
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Style'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "icon-paint.png"
);


$of_options[] = array('name' => trans('Color'),
    'type' => 'info',
    'std' => trans('Color'),
);

$of_options[] = array('name' => trans('Body background color'),
    'type' => 'color',
    'std' => '#fff',
    'id' => 'body-background-color',
);

$of_options[] = array('name' => trans('Primary color'),
    'type' => 'color',
    'std' => '#b40606',
    'id' => 'primary-color',
    'desc' => trans('Primary color is the main color of site.'),
);

$of_options[] = array('name' => trans('Link color'),
    'type' => 'color',
    'std' => '#b40606',
    'id' => 'link-color',
);

$of_options[] = array('name' => trans('Selection Color'),
    'type' => 'color',
    'std' => '',
    'id' => 'selection-color',
    'desc' => trans('Selection color is the background color when you use mouse to select text, elements...'),
);


$of_options[] = array('name' => trans('Options for Dark skin'),
    'type' => 'info',
    'std' => trans('Options for Dark skin'),
);

$of_options[] = array('name' => trans('Enable dark skin?'),
    'type' => 'switch',
    'std' => false,
    'id' => 'dark-skin',
    'desc' => trans('Turn it ON to enable dark skin. Please Note: If you enable dark skin, please go to <strong>Theme Options &raquo; Style</strong> and change body background color to a dark color and go to <strong>Theme Options &raquo; Header</strong>.'),
);

$of_options[] = array('name' => trans('Header background color?'),
    'type' => 'color',
    'id' => 'header-bg-color',
    'desc' => trans('Select header background color for dark skin.'),
);

/* ----------------------------------------------------------------------------------- */
/* Typography
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Typography'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "mac-smz-icon.gif"
);

$of_options[] = array('name' => trans('Upercase/Lowercase'),
    'type' => 'info',
    'std' => trans('Upercase/Lowercase'),
);

$of_options[] = array('name' => trans('Enable uppercase mode'),
    'type' => 'switch',
    'std' => true,
    'id' => 'uppercase',
);

$of_options[] = array('name' => trans('Font family'),
    'type' => 'info',
    'std' => trans('Font family'),
);

$of_options[] = array('name' => trans('Body font'),
    'desc' => trans('You can choose a normal font or Google font'),
    'id' => 'body-font',
    'std' => 'Georgia, serif',
    'type' => 'select_google_font',
    'preview' => array(
        'text' => 'This is the preview!', //this is the text from preview box
        'size' => '30px' //this is the text size from preview box
    ),
    'options' => hcms_google_fonts(),
);

$of_options[] = array('name' => trans('Heading font'),
    'desc' => trans('You can choose a normal font or Google font'),
    'id' => 'heading-font',
    'std' => 'Oswald',
    'type' => 'select_google_font',
    'preview' => array(
        'text' => 'This is the preview!', //this is the text from preview box
        'size' => '30px' //this is the text size from preview box
    ),
    'options' => hcms_google_fonts(),
);

$of_options[] = array('name' => trans('Navigation font'),
    'desc' => trans('You can choose a normal font or Google font'),
    'id' => 'mainnav-font',
    'std' => 'Oswald',
    'type' => 'select_google_font',
    'preview' => array(
        'text' => 'This is the preview!', //this is the text from preview box
        'size' => '30px' //this is the text size from preview box
    ),
    'options' => hcms_google_fonts(),
);

$of_options[] = array('name' => trans('General font size'),
    'type' => 'info',
    'std' => trans('General font size'),
);

$of_options[] = array('name' => trans('Body font size (px)'),
    'type' => 'sliderui',
    'std' => 14,
    'min' => 8,
    'max' => 28,
    'step' => 1,
    'id' => 'body-size',
);

$of_options[] = array('name' => trans('H1 font size (px)'),
    'type' => 'sliderui',
    'std' => 36,
    'min' => 20,
    'max' => 80,
    'step' => 1,
    'id' => 'h1-size',
);

$of_options[] = array('name' => trans('H2 font size (px)'),
    'type' => 'sliderui',
    'std' => 28,
    'min' => 16,
    'max' => 64,
    'step' => 1,
    'id' => 'h2-size',
);

$of_options[] = array('name' => trans('H3 font size (px)'),
    'type' => 'sliderui',
    'std' => 22,
    'min' => 12,
    'max' => 48,
    'step' => 1,
    'id' => 'h3-size',
);

$of_options[] = array('name' => trans('H4 font size (px)'),
    'type' => 'sliderui',
    'std' => 16,
    'min' => 8,
    'max' => 32,
    'step' => 1,
    'id' => 'h4-size',
);

$of_options[] = array('name' => trans('H5 font size (px)'),
    'type' => 'sliderui',
    'std' => 13,
    'min' => 8,
    'max' => 30,
    'step' => 1,
    'id' => 'h5-size',
);

$of_options[] = array('name' => trans('H6 font size (px)'),
    'type' => 'sliderui',
    'std' => 13,
    'min' => 8,
    'max' => 30,
    'step' => 1,
    'id' => 'h6-size',
);

$of_options[] = array('name' => trans('Element font size'),
    'type' => 'info',
    'std' => trans('Element font size'),
);

$of_options[] = array('name' => trans('Blog post title font size (px)'),
    'type' => 'sliderui',
    'std' => 28,
    'min' => 14,
    'max' => 60,
    'step' => 1,
    'id' => 'blog-title-size',
);

$of_options[] = array('name' => trans('Single post title font size (px)'),
    'type' => 'sliderui',
    'std' => 45,
    'min' => 20,
    'max' => 80,
    'step' => 1,
    'id' => 'single-post-size',
);

$of_options[] = array('name' => trans('Page title font size (px)'),
    'type' => 'sliderui',
    'std' => 65,
    'min' => 30,
    'max' => 100,
    'step' => 1,
    'id' => 'page-title-size',
);

$of_options[] = array('name' => trans('Top area Big heading size (px)'),
    'type' => 'sliderui',
    'std' => 100,
    'min' => 50,
    'max' => 200,
    'step' => 2,
    'id' => 'toparea-big-heading-size',
);

/* ----------------------------------------------------------------------------------- */
/* Blog
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Blog'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "icon-docs.png"
);

$of_options[] = array('name' => trans('Blog title'),
    'type' => 'text',
    'id' => 'blog-title',
    'std' => trans('Blog'),
    'desc' => trans('Blog title'),
);

$of_options[] = array('name' => trans('Blog subtitle'),
    'type' => 'text',
    'id' => 'blog-subtitle',
    'std' => trans('Just tell your stories'),
    'desc' => trans('Blog subtitle'),
);

$of_options[] = array('name' => trans('Display content or excerpt'),
    'type' => 'select',
    'id' => 'blog-display',
    'options' => array('excerpt' => trans('Excerpt'), 'content' => trans('Content')),
    'std' => 'excerpt',
    'desc' => trans('Select display post content or excerpt on the blog.'),
);

$of_options[] = array('name' => trans('Featured image crop?'),
    'type' => 'switch',
    'id' => 'blog-thumb-crop',
    'std' => true,
    'desc' => trans('If turned ON, featured images will be fixed in width/height (600x400px). If turned OFF, featured images will be fixed in width and unlimited in height (auto height, suit for portrait images).'),
);

$of_options[] = array('name' => trans('Thumbnail link to post?'),
    'type' => 'switch',
    'id' => 'blog-thumb-link-to-post',
    'std' => true,
    'desc' => trans('Turn it ON if you want the post thumbnail link to the article. Turn it OFF if you do not want so.'),
);

/* Meta Options */
$of_options[] = array('name' => trans('Blog meta options'),
    'type' => 'info',
    'std' => trans('Blog meta options'),
);

$of_options[] = array('name' => trans('Display post date?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'blog-date',
);

$of_options[] = array('name' => trans('Display comments link in post meta'),
    'type' => 'switch',
    'std' => true,
    'id' => 'blog-comments-link',
);

$of_options[] = array('name' => trans('Display categories in post meta'),
    'type' => 'switch',
    'std' => true,
    'id' => 'blog-categories',
);

$of_options[] = array('name' => trans('Display author in post meta'),
    'type' => 'switch',
    'std' => true,
    'id' => 'blog-author',
);

$of_options[] = array('name' => trans('Excerpt length (words)'),
    'type' => 'sliderui',
    'std' => 40,
    'step' => 1,
    'min' => 10,
    'max' => 200,
    'id' => 'excerpt-length',
);

$of_options[] = array('name' => trans('Display "Reamore" link'),
    'type' => 'switch',
    'std' => true,
    'id' => 'blog-readmore',
);

/* 404 */
$of_options[] = array('name' => trans('404 page'),
    'type' => 'info',
    'std' => trans('404 page'),
);

$of_options[] = array('name' => trans('404 Custom Text'),
    'type' => 'textarea',
    'id' => '404-text',
    'std' => trans('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help or go back to <a href="#" rel="home">Homepage</a>.'),
    'desc' => trans('The message you want to say to your audiences when they got to 404 page. HTML is allowed.'),
);

/* ----------------------------------------------------------------------------------- */
/* Single
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Single'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "icon-edit.png"
);

/* Featured Image */
$of_options[] = array('name' => trans('Featured Image'),
    'type' => 'info',
    'std' => trans('Featured Image'),
);

$of_options[] = array('name' => trans('Display featured image on single post'),
    'type' => 'switch',
    'std' => true,
    'id' => 'single-featured-image',
    'desc' => trans('Turn OFF if you don\'t want to display featured image on single post'),
);

$of_options[] = array('name' => trans('Featured image thumbnail crop?'),
    'type' => 'select',
    'options' => array('yes' => trans('Yes'), 'no' => trans('No')),
    'std' => 'nocrop',
    'id' => 'single-thumb-crop',
    'desc' => trans('If choose yes, featured image will be fixed in height (600x400px). Otherwise, featured image will be auto height (600px in width and height depends on each image proportion).'),
);

$of_options[] = array('name' => trans('Featured image link to full image?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'single-featured-image-link-to-full',
    'desc' => trans('Turn OFF if you don\'t want to link featured image thumbnail to fullwidth image.'),
);

/* Meta */
$of_options[] = array('name' => trans('Meta'),
    'type' => 'info',
    'std' => trans('Meta'),
);

$of_options[] = array('name' => trans('Display Next / Previous links?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'single-nav',
    'desc' => trans('Turn OFF if you don\'t want to display post navigation links on single post'),
);

$of_options[] = array('name' => trans('Display tags'),
    'type' => 'switch',
    'std' => true,
    'id' => 'single-tags',
    'desc' => trans('Turn OFF if you don\'t want to display post tags on single post'),
);

$of_options[] = array('name' => trans('Display authorbox'),
    'type' => 'switch',
    'std' => true,
    'id' => 'single-authorbox',
    'desc' => trans('Turn OFF if you don\'t want to display author box on single post'),
);

/* ----------------------------------------------------------------------------------- */
/* Portfolio
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Portfolio'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "work.png"
);

/* Loading Image */
$of_options[] = array('name' => trans('Loading Image'),
    'type' => 'info',
    'std' => trans('Loading Image'),
);

$of_options[] = array('name' => trans('Ajax loader image'),
    'type' => 'media',
    'id' => 'portfolio-ajax-loader-image',
    'desc' => trans('Select a custom ajax loader image for your portfolio'),
);

$of_options[] = array('name' => trans('Retina ajax loader image'),
    'type' => 'media',
    'id' => 'portfolio-retina-ajax-loader-image',
    'desc' => trans('Select ajax loader image with double size.'),
);

/* Labels */
$of_options[] = array('name' => trans('Custom labels'),
    'type' => 'info',
    'std' => trans('Custom labels'),
);

$of_options[] = array('name' => trans('Client label'),
    'type' => 'text',
    'std' => trans('Client:'),
    'id' => 'portfolio-client-label',
    'desc' => trans('Select custom text for the word "Client:"'),
);

$of_options[] = array('name' => trans('Location label'),
    'type' => 'text',
    'std' => trans('Location:'),
    'id' => 'portfolio-location-label',
    'desc' => trans('Select custom text for the word "Location:"'),
);

$of_options[] = array('name' => trans('Date label'),
    'type' => 'text',
    'std' => trans('Date:'),
    'id' => 'portfolio-date-label',
    'desc' => trans('Select custom text for the word "Date:"'),
);

$of_options[] = array('name' => trans('URL label'),
    'type' => 'text',
    'std' => trans('Launch Project:'),
    'id' => 'portfolio-url-label',
    'desc' => trans('Select custom text for the word "URL:"'),
);

$of_options[] = array('name' => trans('Categories label'),
    'type' => 'text',
    'std' => trans('Categories:'),
    'id' => 'portfolio-cat-label',
    'desc' => trans('Select custom text for the word "Categories:"'),
);

/* ----------------------------------------------------------------------------------- */
/* WooCommerce
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('WooCommerce'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "cart.png"
);

/* Menu */
$of_options[] = array('name' => trans('Cart menu icon'),
    'type' => 'info',
    'std' => trans('Cart menu icon'),
);

$of_options[] = array('name' => trans('Display a cart icon on the "Primary menu"'),
    'type' => 'switch',
    'std' => false,
    'id' => 'shop-cart-icon',
    'desc' => trans('Display a cart icon next to all menu items.'),
);

$of_options[] = array('name' => trans('Empty cart should?'),
    'type' => 'select',
    'std' => 'sidebar',
    'options' => array(
        'cart' => trans('Link to Cart page'),
        'shop' => trans('Link to Shop page'),
        'custom' => trans('Link to a custom URL'),
        'hide' => trans('Not display'),
    ),
    'id' => 'shop-cart-menu-empty',
);

$of_options[] = array('name' => trans('Custom link when empty cart'),
    'type' => 'text',
    'std' => '',
    'id' => 'shop-cart-menu-empty-link',
    'desc' => trans('If left blank, it\'ll link to the shop page. If the cart is not empty, it\'ll link to the Cart page.'),
);

/* Shop Archive Page */
$of_options[] = array('name' => trans('Shop Archive'),
    'type' => 'info',
    'std' => trans('Shop Archive'),
);

$of_options[] = array('name' => trans('Shop Archive Template'),
    'type' => 'select',
    'std' => 'sidebar',
    'options' => array(
        'sidebar' => trans('With Sidebar'),
        'fullwidth' => trans('Fullwidth'),
    ),
    'id' => 'shop-template',
);

$of_options[] = array('name' => trans('Sidebar Position'),
    'type' => 'select',
    'std' => 'right',
    'options' => array(
        'right' => trans('Right'),
        'left' => trans('Left'),
    ),
    'id' => 'shop-sidebar-position',
);

$of_options[] = array('name' => trans('Display WooCommerce Breadcrumb'),
    'type' => 'switch',
    'std' => false,
    'id' => 'shop-breadcrumb',
);

$of_options[] = array('name' => trans('Show "sorting"?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'shop-display-sorting',
);

$of_options[] = array('name' => trans('Show "result count"?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'shop-display-result-count',
);

$of_options[] = array('name' => trans('Number of columns (default)'),
    'type' => 'sliderui',
    'std' => 4,
    'min' => 2,
    'max' => 6,
    'id' => 'shop-products-column',
);

$of_options[] = array('name' => trans('Number of products per page'),
    'type' => 'text',
    'std' => '',
    'id' => 'shop-products-per-page',
    'desc' => trans('Fill it -1 if you want to display all products.'),
);

/* Single Product */
$of_options[] = array('name' => trans('Single Product'),
    'type' => 'info',
    'std' => trans('Single Product'),
);

$of_options[] = array('name' => trans('Template'),
    'type' => 'select',
    'std' => 'sidebar',
    'options' => array(
        'sidebar' => trans('With Sidebar'),
        'fullwidth' => trans('Fullwidth'),
    ),
    'id' => 'shop-single-template',
);

$of_options[] = array('name' => trans('Sidebar Position'),
    'type' => 'select',
    'std' => 'right',
    'options' => array(
        'right' => trans('Right'),
        'left' => trans('Left'),
    ),
    'id' => 'shop-single-sidebar-position',
);

$of_options[] = array('name' => trans('Display related products?'),
    'type' => 'switch',
    'std' => true,
    'id' => 'shop-single-display-related-products',
);

$of_options[] = array('name' => trans('Number of related products to show'),
    'type' => 'sliderui',
    'min' => 0,
    'max' => 12,
    'std' => 4,
    'step' => 1,
    'id' => 'shop-related-products-number',
);

$of_options[] = array('name' => trans('Number of items per row'),
    'type' => 'sliderui',
    'min' => 2,
    'max' => 6,
    'std' => 4,
    'step' => 1,
    'id' => 'shop-related-products-column',
);

/* ----------------------------------------------------------------------------------- */
/* Social Icons
  /*----------------------------------------------------------------------------------- */
$of_options[] = array('name' => trans('Social'),
    'type' => 'heading',
    "icon" => OPTIONS_IMAGES . "twitter.png"
);

foreach (hcms_social_array() as $s => $c):

    $of_options[] = array('name' => $c,
        'type' => 'text',
        'std' => '',
        'id' => 'social-' . $s,
    );
endforeach;


$of_options[] = array('name' => trans('Custom social icon'),
    'type' => 'info',
    'std' => trans('Custom social icon'),
);

$of_options[] = array('name' => trans('Custom Social Icon'),
    'type' => 'media',
    'std' => '',
    'id' => 'custom-social-icon',
    'desc' => trans('Upload your own social icon (40x40 png image)'),
);

$of_options[] = array('name' => trans('Custom Social Icon URL'),
    'type' => 'text',
    'std' => '',
    'id' => 'custom-social-icon-url',
);

$of_options[] = array('name' => trans('Custom Social Icon Title'),
    'type' => 'text',
    'std' => '',
    'id' => 'custom-social-icon-title',
);

/* ----------------------------------------------------------------------------------- */
/* Backup Options */
/* ----------------------------------------------------------------------------------- */
$of_options[] = array("name" => trans("Backup Options"),
    "type" => "heading",
    "icon" => OPTIONS_IMAGES . "icon-slider.png"
);

$of_options[] = array("name" => trans("Backup and Restore Options"),
    "id" => "of_backup",
    "std" => "",
    "type" => "backup",
    "desc" => trans('You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back.'),
);

$of_options[] = array("name" => trans("Transfer Theme Options Data"),
    "id" => "of_transfer",
    "std" => "",
    "type" => "transfer",
    "desc" => trans('You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click "Import Options".'),
);
/*
  $of_options[] = array( "name" 		=> "Slider Options",
  "desc" 		=> "Unlimited slider with drag and drop sortings.",
  "id" 		=> "example_slider",
  "std" 		=> "",
  "type" 		=> "slider"
  );
 */

function checked($checked, $current, $echo = true) {
    $str = '';
    if ($checked == $current) {
        $str = 'checked';
    }
    if ($echo == true) {
        echo $str;
    }
    return $str;
}

function selected($selected, $current, $echo = true) {
    $str = '';
    if ($selected == $current) {
        $str = 'checked';
    }
    if ($echo == true) {
        echo $str;
    }
    return $str;
}

function hcms_google_fonts() {

    $fonts = 'ABeeZee,Abel,Abril Fatface,Aclonica,Acme,Actor,Adamina,Advent Pro,Aguafina Script,Akronim,Aladin,Aldrich,Alegreya,Alegreya Sans,Alegreya SC,Alex Brush,Alfa Slab One,Alice,Alike,Alike Angular,Allan,Allerta,Allerta Stencil,Allura,Almendra,Almendra Display,Almendra SC,Amarante,Amaranth,Amatic SC,Amethysta,Anaheim,Andada,Andika,Angkor,Annie Use Your Telescope,Anonymous Pro,Antic,Antic Didone,Antic Slab,Anton,Arapey,Arbutus,Arbutus Slab,Architects Daughter,Archivo Black,Archivo Narrow,Arimo,Arizonia,Armata,Artifika,Arvo,Asap,Asset,Astloch,Asul,Atomic Age,Aubrey,Audiowide,Autour One,Average,Average Sans,Averia Gruesa Libre,Averia Libre,Averia Sans Libre,Averia Serif Libre,Bad Script,Balthazar,Bangers,Basic,Battambang,Baumans,Bayon,Belgrano,Belleza,BenchNine,Bentham,Berkshire Swash,Bevan,Bigelow Rules,Bigshot One,Bilbo,Bilbo Swash Caps,Bitter,Black Ops One,Bokor,Bonbon,Boogaloo,Bowlby One,Bowlby One SC,Brawler,Bree Serif,Bubblegum Sans,Bubbler One,Buda,Buenard,Butcherman,Butterfly Kids,Cabin,Cabin Condensed,Cabin Sketch,Caesar Dressing,Cagliostro,Calligraffitti,Cambo,Candal,Cantarell,Cantata One,Cantora One,Capriola,Cardo,Carme,Carrois Gothic,Carrois Gothic SC,Carter One,Caudex,Cedarville Cursive,Ceviche One,Changa One,Chango,Chau Philomene One,Chela One,Chelsea Market,Chenla,Cherry Cream Soda,Cherry Swash,Chewy,Chicle,Chivo,Cinzel,Cinzel Decorative,Clicker Script,Coda,Coda Caption,Codystar,Combo,Comfortaa,Coming Soon,Concert One,Condiment,Content,Contrail One,Convergence,Cookie,Copse,Corben,Courgette,Cousine,Coustard,Covered By Your Grace,Crafty Girls,Creepster,Crete Round,Crimson Text,Croissant One,Crushed,Cuprum,Cutive,Cutive Mono,Damion,Dancing Script,Dangrek,Dawning of a New Day,Days One,Delius,Delius Swash Caps,Delius Unicase,Della Respira,Denk One,Devonshire,Didact Gothic,Diplomata,Diplomata SC,Domine,Donegal One,Doppio One,Dorsa,Dosis,Dr Sugiyama,Droid Sans,Droid Sans Mono,Droid Serif,Duru Sans,Dynalight,EB Garamond,Eagle Lake,Eater,Economica,Electrolize,Elsie,Elsie Swash Caps,Emblema One,Emilys Candy,Engagement,Englebert,Enriqueta,Erica One,Esteban,Euphoria Script,Ewert,Exo,Expletus Sans,Fanwood Text,Fascinate,Fascinate Inline,Faster One,Fasthand,Federant,Federo,Felipa,Fenix,Finger Paint,Fjalla One,Fjord One,Flamenco,Flavors,Fondamento,Fontdiner Swanky,Forum,Francois One,Freckle Face,Fredericka the Great,Fredoka One,Freehand,Fresca,Frijole,Fruktur,Fugaz One,GFS Didot,GFS Neohellenic,Gafata,Galdeano,Galindo,Gentium Basic,Gentium Book Basic,Geo,Geostar,Geostar Fill,Germania One,Gilda Display,Give You Glory,Glass Antiqua,Glegoo,Gloria Hallelujah,Goblin One,Gochi Hand,Gorditas,Goudy Bookletter 1911,Graduate,Grand Hotel,Gravitas One,Great Vibes,Griffy,Gruppo,Gudea,Habibi,Hammersmith One,Hanalei,Hanalei Fill,Handlee,Hanuman,Happy Monkey,Headland One,Henny Penny,Herr Von Muellerhoff,Holtwood One SC,Homemade Apple,Homenaje,IM Fell DW Pica,IM Fell DW Pica SC,IM Fell Double Pica,IM Fell Double Pica SC,IM Fell English,IM Fell English SC,IM Fell French Canon,IM Fell French Canon SC,IM Fell Great Primer,IM Fell Great Primer SC,Iceberg,Iceland,Imprima,Inconsolata,Inder,Indie Flower,Inika,Irish Grover,Istok Web,Italiana,Italianno,Jacques Francois,Jacques Francois Shadow,Jim Nightshade,Jockey One,Jolly Lodger,Josefin Sans,Josefin Slab,Joti One,Judson,Julee,Julius Sans One,Junge,Jura,Just Another Hand,Just Me Again Down Here,Kameron,Karla,Kaushan Script,Keania One,Kelly Slab,Kenia,Khmer,Kite One,Knewave,Kotta One,Koulen,Kranky,Kreon,Kristi,Krona One,La Belle Aurore,Lancelot,Lato,League Script,Leckerli One,Ledger,Lekton,Lemon,Libre Baskerville,Life Savers,Lilita One,Limelight,Linden Hill,Lobster,Lobster Two,Londrina Outline,Londrina Shadow,Londrina Sketch,Londrina Solid,Lora,Love Ya Like A Sister,Loved by the King,Lovers Quarrel,Luckiest Guy,Lusitana,Lustria,Macondo,Macondo Swash Caps,Magra,Maiden Orange,Mako,Marcellus,Marcellus SC,Marck Script,Margarine,Marko One,Marmelad,Marvel,Mate,Mate SC,Maven Pro,McLaren,Meddon,MedievalSharp,Medula One,Megrim,Meie Script,Merienda,Merienda One,Merriweather,Metal,Metal Mania,Metamorphous,Metrophobic,Michroma,Milonga,Miltonian,Miltonian Tattoo,Miniver,Miss Fajardose,Modern Antiqua,Molengo,Molle,Monda,Monofett,Monoton,Monsieur La Doulaise,Montaga,Montez,Montserrat,Montserrat Alternates,Montserrat Subrayada,Moul,Moulpali,Mountains of Christmas,Mouse Memoirs,Mr Bedfort,Mr Dafoe,Mr De Haviland,Mrs Saint Delafield,Mrs Sheppards,Muli,Mystery Quest,Neucha,Neuton,New Rocker,News Cycle,Niconne,Nixie One,Nobile,Nokora,Norican,Nosifer,Nothing You Could Do,Noticia Text,Nova Cut,Nova Flat,Nova Mono,Nova Oval,Nova Round,Nova Script,Nova Slim,Nova Square,Numans,Nunito,Odor Mean Chey,Offside,Old Standard TT,Oldenburg,Oleo Script,Oleo Script Swash Caps,Open Sans,Open Sans Condensed,Oranienbaum,Orbitron,Oregano,Orienta,Original Surfer,Oswald,Over the Rainbow,Overlock,Overlock SC,Ovo,Oxygen,Oxygen Mono,PT Mono,PT Sans,PT Sans Caption,PT Sans Narrow,PT Serif,PT Serif Caption,Pacifico,Paprika,Parisienne,Passero One,Passion One,Patrick Hand,Patua One,Paytone One,Peralta,Permanent Marker,Petit Formal Script,Petrona,Philosopher,Piedra,Pinyon Script,Pirata One,Plaster,Play,Playball,Playfair Display,Playfair Display SC,Podkova,Poiret One,Poller One,Poly,Pompiere,Pontano Sans,Port Lligat Sans,Port Lligat Slab,Prata,Preahvihear,Press Start 2P,Princess Sofia,Prociono,Prosto One,Puritan,Purple Purse,Quando,Quantico,Quattrocento,Quattrocento Sans,Questrial,Quicksand,Quintessential,Qwigley,Racing Sans One,Radley,Raleway,Raleway Dots,Rambla,Rammetto One,Ranchers,Rancho,Rationale,Redressed,Reenie Beanie,Revalia,Ribeye,Ribeye Marrow,Righteous,Risque,Roboto,Roboto Condensed,Rochester,Rock Salt,Rokkitt,Romanesco,Ropa Sans,Rosario,Rosarivo,Rouge Script,Ruda,Rufina,Ruge Boogie,Ruluko,Rum Raisin,Ruslan Display,Russo One,Ruthie,Rye,Sacramento,Sail,Salsa,Sanchez,Sancreek,Sansita One,Sarina,Satisfy,Scada,Schoolbell,Seaweed Script,Sevillana,Seymour One,Shadows Into Light,Shadows Into Light Two,Shanti,Share,Share Tech,Share Tech Mono,Shojumaru,Short Stack,Siemreap,Sigmar One,Signika,Signika Negative,Simonetta,Sirin Stencil,Six Caps,Skranji,Slackey,Smokum,Smythe,Sniglet,Snippet,Snowburst One,Sofadi One,Sofia,Sonsie One,Sorts Mill Goudy,Source Code Pro,Source Sans Pro,Special Elite,Spicy Rice,Spinnaker,Spirax,Squada One,Stalemate,Stalinist One,Stardos Stencil,Stint Ultra Condensed,Stint Ultra Expanded,Stoke,Strait,Sue Ellen Francisco,Sunshiney,Supermercado One,Suwannaphum,Swanky and Moo Moo,Syncopate,Tangerine,Taprom,Telex,Tenor Sans,Text Me One,The Girl Next Door,Tienne,Tinos,Titan One,Titillium Web,Trade Winds,Trocchi,Trochut,Trykker,Tulpen One,Ubuntu,Ubuntu Condensed,Ubuntu Mono,Ultra,Uncial Antiqua,Underdog,Unica One,UnifrakturCook,UnifrakturMaguntia,Unkempt,Unlock,Unna,VT323,Vampiro One,Varela,Varela Round,Vast Shadow,Vibur,Vidaloka,Viga,Voces,Volkhov,Vollkorn,Voltaire,Waiting for the Sunrise,Wallpoet,Walter Turncoat,Warnes,Wellfleet,Wendy One,Wire One,Yanone Kaffeesatz,Yellowtail,Yeseva One,Yesteryear,Zeyada';

    $font_array = explode(',', $fonts);
    foreach ($font_array as $font) {
        $font = trim($font);
        $google_fonts_array[$font] = $font;
    }

    $normal_fonts = "Palatino Linotype', 'Book Antiqua', Palatino, serif|'Times New Roman', Times, serif|Arial, Helvetica, sans-serif|'Arial Black', Gadget, sans-serif|'Comic Sans MS', cursive, sans-serif|Impact, Charcoal, sans-serif|'Lucida Sans Unicode', 'Lucida Grande', sans-serif|Tahoma, Geneva, sans-serif|'Trebuchet MS', Helvetica, sans-serif|Verdana, Geneva, sans-serif|'Courier New', Courier, monospace|'Lucida Console', Monaco, monospace";
    $normal_fonts = explode('|', $normal_fonts);
    $normal_fonts_arr = array();
    foreach ($normal_fonts as $f) {
        $normal_fonts_arr[$f] = $f;
    }
    //return $normal_fonts_arr; 
    return array_merge($normal_fonts_arr, $google_fonts_array);
}

/* ------------------------------------------------------------------------------ */
/* Social
  /* ------------------------------------------------------------------------------ */

function hcms_social_array() {
    return array(
        'facebook' => trans('Facebook'),
        'twitter' => trans('Twitter'),
        'google-plus' => trans('Google+'),
        'linkedin' => trans('LinkedIn'),
        'tumblr' => trans('Tumblr'),
        'pinterest' => trans('Pinterest'),
        'youtube' => trans('YouTube'),
        'skype' => trans('Skype'),
        'instagram' => trans('Instagram'),
        'delicious' => trans('Delicious'),
        'reddit' => trans('Reddit'),
        'stumbleupon' => trans('StumbleUpon'),
        'wordpress' => trans('Wordpress'),
        'joomla' => trans('Joomla'),
        'blogger' => trans('Blogger'),
        'vimeo' => trans('Vimeo'),
        'yahoo' => trans('Yahoo!'),
        'flickr' => trans('Flickr'),
        'picasa' => trans('Picasa'),
        'deviantart' => trans('DeviantArt'),
        'github' => trans('GitHub'),
        'stackoverflow' => trans('StackOverFlow'),
        'xing' => trans('Xing'),
        'flattr' => trans('Flattr'),
        'foursquare' => trans('Foursquare'),
        'paypal' => trans('Paypal'),
        'yelp' => trans('Yelp'),
        'soundcloud' => trans('SoundCloud'),
        'lastfm' => trans('Last.fm'),
        'lanyrd' => trans('Lanyrd'),
        'dribbble' => trans('Dribbble'),
        'forrst' => trans('Forrst'),
        'steam' => trans('Steam'),
        'behance' => trans('Behance'),
        'mixi' => trans('Mixi'),
        'weibo' => trans('Weibo'),
        'renren' => trans('Renren'),
        'evernote' => trans('Evernote'),
        'dropbox' => trans('Dropbox'),
        'bitbucket' => trans('Bitbucket'),
        'trello' => trans('Trello'),
        'vk' => trans('VKontakte'),
        'home' => trans('Homepage'),
        'envelope-alt' => trans('Email'),
        'rss' => trans('RSS'),
    );
}
