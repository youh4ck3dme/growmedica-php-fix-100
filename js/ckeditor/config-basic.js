/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 * Galéria
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for a single toolbar row.
	config.toolbarGroups = [
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'forms' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'tools' },
		{ name: 'others' },
		{ name: 'about' }
	];

    config.allowedContent = true;
	config.language = 'sk';
	// The default plugins included in the basic setup define some buttons that
	// are not needed in a basic editor. They are removed here.
	config.removeButtons = 'Source,Cut,Copy,Paste,Undo,Redo,Anchor,Strike,Styles,Maximize,Quote,Image,Table,SpecialChar,Blockquote,PasteFromWord,Underline,Subscript,Superscript,Format,Iframe,HorizontalRule,SelectAll,Scayt,PageBreak,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Save,NewPage,Preview,Print,Templates,Font,TextColor,BGColor,Find,Replace,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Flash,Smiley,FontSize,ShowBlocks,gg,Youtube,lineheight';

	// Dialog windows are also simplified.
	config.removeDialogTabs = 'link:advanced';
};
