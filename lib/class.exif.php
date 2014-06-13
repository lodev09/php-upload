<?php

/**
 * @package Exif Class in PHP5
 * @author Jovanni Lo
 * @link http://www.lodev09.com
 * @copyright 2014 Jovanni Lo, all rights reserved
 * @license 
 * The MIT License (MIT)
 * Copyright (c) 2014 Jovanni Lo
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

class Exif {

	private $_file;
	private $_exif;
	public function __construct($filename) {
		$this->_exif = exif_read_data($filename);
		if (!$this->_exif) self::_err('Cannot read EXIF data from '.$filename);
	}

	private static function _err($err) {
		trigger_error($err);
	}

	public function get_data() {
		return $this->_exif;
	}

	public function get_gps() {
		if (!isset($this->_exif['GPSLatitude']) || !isset($this->_exif['GPSLongitude']))
			return false;

		$gps2num = function($coord_part) {
			$parts = explode('/', $coord_part);
		    if (count($parts) <= 0)
		        return 0;

		    if (count($parts) == 1)
		        return $parts[0];

		    return floatval($parts[0]) / floatval($parts[1]);
		};

		$gps = function($exif_coord, $hemi) use ($gps2num) {
			$degrees = count($exif_coord) > 0 ? $gps2num($exif_coord[0]) : 0;
		    $minutes = count($exif_coord) > 1 ? $gps2num($exif_coord[1]) : 0;
		    $seconds = count($exif_coord) > 2 ? $gps2num($exif_coord[2]) : 0;

		    $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

		    return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
		};

		$exif = $this->_exif;
		$time = function() use ($exif) {
			if (isset($exif['DateTimeOriginal']))
				return $exif['DateTimeOriginal'];
			else {
				if (isset($exif['DateTime']))
					return $exif['DateTime'];
				else return null;
			}
		};

		$gps_info = new stdClass;
		$gps_info->lat = isset($this->_exif['GPSLatitude']) ? $gps($this->_exif['GPSLatitude'], $this->_exif['GPSLatitudeRef']) : 0;
		$gps_info->lng = isset($this->_exif['GPSLongitude']) ? $gps($this->_exif['GPSLongitude'], $this->_exif['GPSLongitudeRef']) : 0;
		$gps_info->time = $time();
		
		return $gps_info;
	}

}

?>