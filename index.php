<?php

require_once('lib/class.fileupload.php');
require_once('lib/class.exif.php');

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

?>

<html>
<body>

<form action="index.php" method="post" enctype="multipart/form-data">
	<label for="file">Files: </label>
	<input type="file" name="files[]" multiple /><br>

	<input type="submit" name="submit" value="Submit" />
</form>

</body>
</html>