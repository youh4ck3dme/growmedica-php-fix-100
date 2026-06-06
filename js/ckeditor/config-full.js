/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 * Newsletter
 */

CKEDITOR.editorConfig = function (config) {
    config.width = "810px";
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


    // Remove some buttons provided by the standard plugins, which are
    // not needed in the Standard(s) toolbar.
    config.removeButtons = 'Save,NewPage,Preview,Templates,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Language,Font';

    // Set the most common block elements.
    config.format_tags = 'p;h1;h2;h3;pre';
    config.language = 'sk';

    config.allowedContent = true;
    config.image2_alignClasses = ['align-left', 'align-center', 'align-right'];
    // Simplify the dialog windows.
    //config.removeDialogTabs = 'image:advanced;link:advanced';

};
