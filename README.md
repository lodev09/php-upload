PHP FileUpload Class
============================
A very simple yet useful helper class for PHP file upload. Requires `php 5.3` or higher.

## Features
- Supports Multiple file uploads
- Supports Exif for images
- Built-in and Custom validations
- Simple implementation
- or you can ask for more ...

## Install
```php
require_once('lib/class.fileupload.php');
require_once('lib/class.exif.php'); // optional
```

## Usage

### HTML
```html
<html>
<body>

<form action="index.php" method="post" enctype="multipart/form-data">
	<label for="file">Files: </label>
	<input type="file" name="files[]" multiple /><br>

	<input type="submit" name="submit" value="Submit" />
</form>

</body>
</html>
```

### PHP
```php
if (isset($_FILES['files'])) {
	$validations = array(
		'category' => array('document', 'image', 'video'), // validate only those files within this list
		'size' => 20 // maximum of 20mb
	);

	// create new instance
	$upload = new FileUpload($_FILES['files'], $validations);

	// for each file
	foreach ($upload->files as $file) {
		if ($file->validate()) {
			// do your thing on this file ...
			// ...
			// say we don't allow audio files
			if ($file->is('audio')) $error = 'Audio not allowed';
			else {
				// then get base64 encoded string to do something else ...
				$filedata = $file->get_base64();

				// or get the GPS info ...
				$gps = $file->get_exif_gps();

				// then we move it to 'path/to/my/uploads'
				$result = $file->put('path/to/my/uploads');
				$error = $result ? '' : 'Error moving file';
			}
			
		} else {
			// oopps!
			$error = $file->get_error();
		}

		echo $file->name.' - '.($error ? ' [FAILED] '.$error : ' Succeeded!');
		echo '<br />'; 
	}
}
```
You noticed that we loop each `files` using `foreach`, alternatively, you can use the class' `each(...)` method. E.g:
```php
$upload->each(function($file) {
	// do the same thing as above ...
});
```
## Validations
The class has built-in validations (common ones) but you can still add your own though.
```php
$validations = array(
	// built-in validations ...
	// you can pass an array or string here and it will default to the 'is' key
	'extension' => array(
		'is' => array(), // default key
		'not' => array(),
		'message' => ''
	),
	'category' => array(
		'is' => array(), // default key
		'not' => array(),
		'message' => array()
	),
	'size' => array(
		'max' => 20, // default key
		'min' => 0,
		'unit' => 'mb',
	),
	// Custom validations
	// you can pass an array of closure functions for multiple custom validations
	'custom' => function($file) { 
		if ($file->is('audio')) {
			return 'Audio files are not allowed'
		} else return true;
	}
);
```

## The `File` Class
A sample `var_dump`
```php
object(File)[8]
  public '_exif' => null
  private '_validations' => 
    array (size=4)
      'extension' => 
        array (size=0)
          empty
      'category' => 
        array (size=3)
          0 => string 'document' (length=8)
          1 => string 'image' (length=5)
          2 => string 'video' (length=5)
      'size' => int 20
      'custom' => null
  private '_errors' => 
    array (size=0)
      empty
  private '_error_messages' => 
    array (size=11)
      0 => string 'There is no error, the file uploaded with success.' (length=50)
      1 => string 'The uploaded file exceeds the upload_max_filesize directive in php.ini.' (length=71)
      2 => string 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.' (length=90)
      3 => string 'The uploaded file was only partially uploaded.' (length=46)
      4 => string 'No file was uploaded.' (length=21)
      6 => string 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.' (length=67)
      7 => string 'Failed to write file to disk. Introduced in PHP 5.1.0.' (length=54)
      8 => string 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop;examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.' (length=217)
      100 => string 'File type not allowed' (length=21)
      101 => string '[category: video] File not allowed' (length=34)
      102 => string '[size (kb): 11430071] File size not allowed' (length=43)
  public 'name' => string 'VIDEO_3.mp4' (length=11)
  public 'type' => string 'video/mp4' (length=9)
  public 'tmp_name' => string 'C:\wamp\tmp\php4E56.tmp' (length=23)
  public 'error' => int 0
  public 'size' => int 11430071
  public 'extension' => string '.mp4' (length=4)
  public 'category' => string 'video' (length=5)
```

### Categories
Categories are defined currently by the following:
```php
...
switch (strtolower($extetion)) {
    case ".pdf":
    case ".doc":
    case ".rtf":
    case ".txt":
    case ".docx":
    case ".xls":
    case ".xlsx":
        $category = 'document';
        break;
    case ".png":
    case ".jpg":
    case ".jpeg":
    case ".gif":
    case ".bmp":
    case ".psd":
    case ".tif":
    case ".tiff":
        $category = "image";
        break;
    case ".mp3":
    case ".wav":
    case ".wma":
    case ".m4a":
    case ".m3u":
        $category = "audio";
        break;
    case ".3g2":
    case ".3gp":
    case ".asf":
    case ".asx":
    case ".avi":
    case ".flv":    
    case ".m4v":
    case ".mov":
    case ".mp4":
    case ".mpg":
    case ".srt":
    case ".swf":
    case ".vob":
    case ".wmv":
        $category = "video";
        break;
    default:
        $category = "other";
        break;
}
...
```
### Additional Methods
```php
...
// you can set your own error message by specifying the type of error
$file->set_error_message(File::UPLOAD_ERR_EXTENSION_FILTER, 'File type is not allowed');
...
```

## Feedback
All bugs, feature requests, pull requests, feedback, etc., are welcome. Visit my site at [www.lodev09.com](http://www.lodev09.com "www.lodev09.com") or email me at [lodev09@gmail.com](mailto:lodev09@gmail.com)

## Credits
Copyright (c) 2011-2014 - Programmed by Jovanni Lo / [@lodev09](http://twitter.com/lodev09)  

## License
Released under the [MIT License](http://opensource.org/licenses/MIT).
See [LICENSE](LICENSE) file.
