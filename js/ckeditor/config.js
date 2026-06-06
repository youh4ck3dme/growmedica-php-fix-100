/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 * Štruktúra, novinky, produkty, ....
 */

CKEDITOR.editorConfig = function (config) {
    //config.width = "800px";
    config.toolbarGroups = [
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker']},
        {name: 'links'},
        {name: 'insert'},
        {name: 'forms'},
        {name: 'others'},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi']},
        {name: 'styles'},
        {name: 'colors'},
        {name: 'tools'},
        {name: 'about'}
    ];

    config.removeButtons = 'Save,NewPage,Preview,Templates,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Language,Font';

    // Set the most common block elements.
    config.format_tags = 'p;h1;h2;h3;pre';
    config.language = 'sk';

    config.extraPlugins = 'youtube';
    config.allowedContent = true;
    config.image2_alignClasses = ['align-left', 'align-center', 'align-right'];
    
    

    config.filebrowserBrowseUrl = '../js/ckeditor/plugins/pdw_file_browser/index.php?editor=ckeditor';
    config.filebrowserImageBrowseUrl = '../js/ckeditor/plugins/pdw_file_browser/index.php?editor=ckeditor&filter=image';
    config.filebrowserFlashBrowseUrl = '../js/ckeditor/plugins/pdw_file_browser/index.php?editor=ckeditor&filter=flash';

    // Simplify the dialog windows.
    //config.removeDialogTabs = 'image:advanced;link:advanced';

};
