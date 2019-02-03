PHP FileUpload Class
============================
A very simple yet useful helper class to handle PHP file uploads.

## Features
- Supports Multiple file uploads
- Supports Exif for images
- Built-in and Custom validations
- Simple implementation
- or you can ask for more ...

## Installation
```term
$ composer require lodev09/php-upload
```

## Usage

### HTML
```html
<form action="index.php" method="post" enctype="multipart/form-data">
    <input type="file" name="files[]" multiple /><br>
    <input type="submit" name="submit" value="Submit" />
</form>
```

### PHP (server side)
```php
use \Upload\Upload;

if (isset($_FILES['files'])) {
    $validations = array(
        'category' => array('document', 'image', 'video'), // validate only those files within this list
        'size' => 20 // maximum of 20mb
    );

    // create new instance
    $upload = new Upload($_FILES['files'], $validations);

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

?>
```

## Feedback
All bugs, feature requests, pull requests, feedback, etc., are welcome. Visit my site at [www.lodev09.com](http://www.lodev09.com "www.lodev09.com") or email me at [lodev09@gmail.com](mailto:lodev09@gmail.com)

## Credits
&copy; 2018 - Coded by Jovanni Lo / [@lodev09](http://twitter.com/lodev09)

## License
Released under the [MIT License](http://opensource.org/licenses/MIT).
See [LICENSE](LICENSE) file.
