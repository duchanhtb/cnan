/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
    config.language = current_lang;
    config.skin = 'moonocolor';
    config.width = '100%';
    config.height = '600';
	// config.uiColor = '#AADC6E';
    
    config.filebrowserBrowseUrl      =  admin_url + 'browser.php?view=grid&search-type=file';
    config.filebrowserImageBrowseUrl =  admin_url + 'browser.php?view=grid&search-type=image';
    config.filebrowserFlashBrowseUrl =  admin_url + 'browser.php?view=grid&search-type=flash';
    config.filebrowserUploadUrl      =  admin_url + 'upload.php?type='+type;
    config.filebrowserImageUploadUrl =  admin_url + 'upload.php?type='+type;
    config.filebrowserFlashUploadUrl =  admin_url + 'upload.php?type='+type;
    
    config.filebrowserWindowWidth = '800';
    config.filebrowserWindowHeight = '600';
    
    config.extraPlugins = 'youtube,quicktable,imageresize,wenzgmap,oembed,widget';
    
    config.oembed_maxWidth = '560';
	config.oembed_maxHeight = '315';
	config.oembed_WrapperClass = 'embededContent';
    
    config.toolbar = 'MyToolbar';
    config.toolbar_MyToolbar =
    [
        ['Source','Save', 'NewPage', 'Preview', 'Print', 'Templates'],
        ['Style','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],   
        ['Bold','Italic','Underline','Strike','Subscript', 'Superscript', '-'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
        ['Link','Unlink','Underline','Anchor'],
        ['RemoveFormat'],
        [ 'Styles', 'Format', 'Font', 'FontSize',  'TextColor', 'BGColor', 'Table' ],
        ['Image','Youtube','oembed', 'Flash','wenzgmap', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ],
        ['Maximize']
    ];
    
};
