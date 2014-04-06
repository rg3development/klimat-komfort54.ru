<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',              'rb');
define('FOPEN_READ_WRITE',            'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',    'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',          'ab');
define('FOPEN_READ_WRITE_CREATE',       'a+b');
define('FOPEN_WRITE_CREATE_STRICT',       'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',    'x+b');

define('SITE_NAME', 'RG3 DEVELOPMENT');

// IMAGE AND FILE UPLOAD CONSTS
define('IMAGEPATH', dirname(dirname(dirname(__FILE__))).'/www/upload/images/');
define('IMAGESRC','/upload/images/');
define('EDITORPATH', dirname(dirname(dirname(__FILE__))).'/www/upload/editor/');
define('EDITORSRC','/upload/editor/');

// EMAIL CONSTS
define('EMAIL','info@rg3.su');
define('MY_EMAIL','info@rg3.ru');

// GALLERY CONSTS
define('GALLERY_UPLOAD_SIZE','2048');
define('GALLERY_UPLOAD_W','3000');
define('GALLERY_UPLOAD_H','3000');
define('GALLERY_RESIZE_W','200');
define('GALLERY_RESIZE_H','150');
define('GALLERY_RESIZE_CARUSEL_W','162');
define('GALLERY_RESIZE_CARUSEL_H','108');

/* BANNER MAIN IMAGE CONSTS */
define('BANNER_MAIN_IMAGE_MAX_WIDTH', 800);
define('BANNER_MAIN_IMAGE_MAX_HEIGHT', 600);
define('BANNER_MAIN_IMAGE_MAX_SIZE', 2048);
define('BANNER_MAIN_IMAGE_THUMB_W', 757);
define('BANNER_MAIN_IMAGE_THUMB_H', 259);
define('BANNER_MAIN_IMAGE_TOOLTIP', sprintf('фиксированный размер %dx%d px не более %d kB', BANNER_MAIN_IMAGE_MAX_WIDTH, BANNER_MAIN_IMAGE_MAX_HEIGHT, BANNER_MAIN_IMAGE_MAX_SIZE));
/* BANNER ALTERNATIVE IMAGE CONST */
define('BANNER_ALT_IMAGE_MAX_WIDTH', 400);
define('BANNER_ALT_IMAGE_MAX_HEIGHT', 300);
define('BANNER_ALT_IMAGE_MAX_SIZE', 1024);
define('BANNER_ALT_IMAGE_THUMB_W', 180);
define('BANNER_ALT_IMAGE_THUMB_H', 128);
define('BANNER_ALT_IMAGE_TOOLTIP', sprintf('фиксированный размер %dx%d px не более %d kB', BANNER_ALT_IMAGE_MAX_WIDTH, BANNER_ALT_IMAGE_MAX_HEIGHT, BANNER_ALT_IMAGE_MAX_SIZE));

/* NEWS IMAGE CONSTS */
define('NEWS_IMAGE_MAX_WIDTH', 1000);
define('NEWS_IMAGE_MAX_HEIGHT', 1000);
define('NEWS_IMAGE_MAX_SIZE', 2048);
define('NEWS_IMAGE_THUMB_W', 124);
define('NEWS_IMAGE_THUMB_H', 124);
define('NEWS_IMAGE_TOOLTIP', sprintf('максимальный размер %dx%d px не более %d kB', NEWS_IMAGE_MAX_WIDTH, NEWS_IMAGE_MAX_HEIGHT, NEWS_IMAGE_MAX_SIZE));

/* COMMENTS CONSTS */
// Значение по умолчанию для комментариев (1/0 - отображать сразу/требует проверки)
define('COMMENTS_DEFAULT_VALUE', 0);
// сообщение при COMMENTS_DEFAULT_VALUE = 0
define('COMMENTS_APPROVED_MESSAGE', 'Спасибо за Ваш ответ. Комментарий появится после проверки модератором.');
// сообщение при COMMENTS_DEFAULT_VALUE = 1
define('COMMENTS_UNAPPROVED_MESSAGE', 'Спасибо за Ваш ответ.');

/* FEEDBACK CONSTS */
define('FEEDBACK_SUBJECT', 'Сообщение с сайта');  // тема письма
define('FEEDBACK_FROM_EMAIL', 'your@email.ru');   // отправитель (почта)
define('FEEDBACK_FROM_NAME', 'Your_Name');        // отправитель (имя)

/* CONTENT WIDGETS CONSTS */
define('WIDGET_FILE_NAME', 'widgets.php');
define('WIDGET_FILE_PATH', dirname(dirname(__FILE__)) . '/views/custom/' . WIDGET_FILE_NAME);

// custom templates folder (/application/views/your_folder_name/)
define('CUSTOM_THEMES_DIRECTORY', 'custom');

/* End of file constants.php */
/* Location: ./application/config/constants.php */