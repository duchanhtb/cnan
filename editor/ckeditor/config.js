/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
    config.language     = 'vi';
    config.skin         = 'moonocolor';
    config.width        = '100%';
    config.height       = '600';
	// config.uiColor = '#AADC6E';
    
    config.filebrowserWindowWidth = '90%';
    config.filebrowserWindowHeight = '90%';
    
    config.extraPlugins = 'youtube,quicktable,imageresize,wenzgmap,oembed,widget';
    
    config.oembed_maxWidth = '560';
	config.oembed_maxHeight = '315';
	config.oembed_WrapperClass = 'embededContent';
    
    config.toolbar = 'MyToolbar';
    config.toolbar_MyToolbar =
    [
        ['Source','Save'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','-','Bold','Italic','Underline','-','NumberedList','BulletedList','Blockquote'],        
        [ 'Styles', 'Format', 'FontSize',  'TextColor', 'BGColor', 'Table' ],
        ['Link','Unlink','Underline','RemoveFormat'],
        ['Image','Youtube','oembed', 'Flash','wenzgmap'],
        ['Maximize']
    ];
    
};
