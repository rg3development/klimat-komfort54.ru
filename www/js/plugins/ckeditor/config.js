/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    config.language                     = 'ru';
    config.defaultLanguage              = "ru"; 
    config.filebrowserImageBrowseUrl    = "/js/plugins/ckfinder/ckfinder.html?type=Images";
    config.filebrowserImageUploadUrl    = "/js/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images";
    config.toolbar = [
    { name: 'document', items: [ 'Source','-', 'Undo', 'Redo','-','NewPage', 'Templates', 'Find','SelectAll','RemoveFormat','-','Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord','-','HorizontalRule','SpecialChar'] },
    '/',
    { name: 'fonts', items: ['Format','Font','FontSize','TextColor','BGColor']},
    '/',
    { name: 'basicstyles', items: [ 'Bold', 'Italic','Underline', 'Strike', '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','-','NumberedList', 'BulletedList', '-', 'Outdent', 'Indent','Table', '-','Image','Flash','Link', 'Unlink', 'Anchor']},
    ];
};