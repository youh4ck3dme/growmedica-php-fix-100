/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 * 
 * 
 * 
 * Galéria popis
 */

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here.
    // For complete reference see:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config
    config.language = 'sk';
    // The default plugins included in the basic setup define some buttons that
    // are not needed in a basic editor. They are removed here.
    config.removeButtons = 'Source,Cut,Copy,Paste,Undo,Redo,Anchor,Strike,Styles,Maximize,Quote,Image,Table,SpecialChar,Blockquote,PasteFromWord,Underline,Subscript,Superscript,Format,Iframe,HorizontalRule,SelectAll,Scayt,PageBreak,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Save,NewPage,Preview,Print,Templates,Font,TextColor,BGColor,Find,Replace,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Flash,Smiley,FontSize,ShowBlocks,gg';
    // Dialog windows are also simplified.
    config.removeDialogTabs = 'link:advanced';
    config.resize_enabled = false;
    config.removePlugins = 'elementspath';
    config.height = '200px';
};