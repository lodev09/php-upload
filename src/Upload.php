<?php

/**
 * @package Upload Class in PHP7
 * @author Jovanni Lo
 * @link http://www.lodev09.com
 * @copyright 2014 Jovanni Lo, all rights reserved
 * @license
 * The MIT License (MIT)
 * Copyright (c) 2019 Jovanni Lo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Upload;

class Upload {

	/**
	 * files array for mutliple
	 * @var array
	 */
	public $files = [];

	/**
	 * file for single
	 * @var [type]
	 */
	public $file;

	/**
	 * raw data of the file upload
	 * @var [type]
	 */
	private $_raw;

	/**
	 * construct the class
	 * @param array $files_data  data from $_FILES['name']
	 * @param array  $validations validation array
	 */
	public function __construct($files_data, $validations = []) {
		$this->_raw = $files_data;
		if ($this->_raw ) {
			// check if it's multiple or single file upload
			if ($this->_is_multiple()) {
				foreach ($this->_raw['error'] as $key => $error) {
					if (!$this->_raw['name'][$key]) continue;

					$file = new File([
						'name' => $this->_raw['name'][$key],
						'type' => $this->_raw['type'][$key],
						'tmp_name' => $this->_raw['tmp_name'][$key],
						'error' => $error,
						'size' => $this->_raw['size'][$key],
					], $validations);

					$this->files[] = $file;
				}
				// let the single "file" property be the first file (index 0)
				if ($this->files) $this->file = $this->files[0];

			} else if ($this->_raw['name']) {
				$file_info = [];
				foreach ($this->_raw as $key => $value) {
					$file_info[$key] = $value;
				}
				$file = new File($file_info, $validations);

				$this->files[] = $file;
				$this->file = $this->files[0];
			}
		}
	}

	/**
	 * loop through each file
	 * @param  closure $callback callback function for each file
	 */
	public function each($callback) {
		if (is_object($obj) && ($obj instanceof \Closure)) {
			foreach ($this->files as $file) {
				$callback($file);
			}
		}
	}

	/**
	 * check if the upload data is multiple or not
	 * @return boolean true if multiple, otherwise false
	 */
	private function _is_multiple() {
		return is_array($this->_raw['name']);
	}
}
?>