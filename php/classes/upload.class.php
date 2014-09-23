<?php

	class Upload {
		
		/**
		* Holds the file sent by post method
		*
		* @access private
		* @type file
		*
		*/
		private $_file_;
		
		/**
		* Holds the destination folder
		*
		* @access private
		* @type string
		*
		*/
		private $destination_folder;
		
		/**
		* Holds the max size
		*
		* @access private
		* @type int
		*
		*/
		private $file_max_size;
		
		/**
		* Holds the file type
		*
		* @access private
		* @type string
		*
		*/
		private $file_type;
		
		/**
		* Holds the file extention
		*
		* @access private
		* @type string
		*
		*/
		private $file_extention;
		
		/**
		* Holds the new name if there is one
		*
		* @access private
		* @type string
		*
		*/
		private $file_new_name;
		
		/**
		* Holds if the file has to replace an old one
		*
		* @access private
		* @type bool
		*
		*/
		private $file_replace;
		
		/**
		* Constructor
		*
		* @param string $destination - file destination
		* @param file $file - the file 
		* @param int $max_size - file max size
		* @param string $type - file type
		* @param string $extention - file extention
		* @param string $new_name - file new_name
		* @param bool $replace - file replacement
		*
		* @set $this->destination_folder
		* @set $this->file_max_size
		* @set $this->file_type
		* @set $this->file_extention
		* @set $this->file_new_name
		* @set $this->file_replace
		*
		* @use $this->sendOutput()
		* @use $this->testDestination()
		* @use $this->checkFile()
		*
		*/
		function __construct($destination = null, $file = null, $max_size = null, $type = null, $extention = null, $new_name = null, $replace = null) {
			if ($this->testDestination($destination)) {
				$this->destination_folder = $destination;
				$this->file_max_size = is_numeric($max_size) ? $max_size : ini_get('upload_max_filesize');
				$this->file_type = $type;
				$this->file_extention = $extention;
				$this->file_new_name = $new_name;
				$this->file_replace = $replace;
				
				if (is_uploaded_file($file['tmp_name'])) {
					$this->checkFile($file);
				}
				else {
					$this->sendOutput('File not sent.');
				}
			}
			else {
				$this->sendOutput('Destination does not exist.');
			}
		}
		
		/**
		* Test the destination directory
		*
		* @param string $destination - file destination (folder)
		* @return bool
		*
		*/
		private function testDestination($destination) {
			return is_dir($destination);
		}
		
		/**
		* Check file attributes before uplaoding the file
		*
		* @param file $file - the file
		*
		* @set $this->_file_
		*
		* @use $this->checkFileSize()
		* @use $this->checkFileExtention()
		* @use $this->checkFileType()
		* @use $this->sendOutput()
		* @use $this->prepareUpload()
		*
		*/
		private function checkFile($file = null) {
			if ($file == null) sendOutput('File not sent'); 	
			
			$this->_file_ = $file;
			$errors = array();
			
			array_push($errors, $this->checkFileSize());
			array_push($errors, $this->checkFileExtention());
			array_push($errors, $this->checkFileType());
			
			if (in_array(0, $errors)) {
				$this->sendOutput('File triggered errors.');
			}
			else {
				$this->prepareUpload();
			}
		}
		
		/**
		* Check file size attribute
		*
		* @use $this->file_max_size
		* @use $this->_file_
		*
		* @return bool
		*
		*/
		private function checkFileSize() {
			return ($this->_file_['size'] <= $this->file_max_size);
		}
		
		/**
		* Check file extention
		*
		* @use $this->file_extention
		* @use $this->_file_
		*
		* @return bool
		*
		*/
		private function checkFileExtention() {
			$name_pieces = explode('.', $this->_file_['name']);
			$ext = end($name_pieces);
			
			return ($ext == $this->file_extention);
		}
		
		/**
		* Check file type
		*
		* @use $this->file_type
		* @use $this->_file_
		*
		* @return bool
		*
		*/
		private function checkFileType() {
			return ($this->_file_['type'] <= $this->file_type);
		}
		
		/**
		* Prepare the upload the file
		*
		* @use $this->file_replace 
		* @use $this->file_new_name 
		* @use $this->_file_ 
		* @use $this->checkName()
		* @use $this->uplaod()
		*
		* @return bool
		*
		*/
		private function prepareUpload() {
			if (trim($this->file_new_name) == '') {
				$this->file_new_name = $this->_file_['name'];
			}
			
			$this->file_new_name = preg_replace("/[^A-Z0-9._-]/i", '', $this->file_new_name);
			
			if (!$this->file_replace) {
				$this->checkName();
			}
		}
		
		/**
		* Check if file exists and change name
		*
		* @use $this->destination_folder
		* @use $this->file_new_name
		*
		*/
		private function checkName() {
			while (file_exists($this->destination_folder.$this->file_new_name)) {
				$pieces = explode('.', $this->file_new_name);
				$before_last = $pieces[sizeof($pieces)-2];
				$last_letter = substr($before_last, -1);
				$new_file_name = '';
				
				if (is_numeric($last_letter)) {
					$last_letter++;
					$before_last = substr($before_last, 0, -1).$last_letter;
				}
				else {
					$last_letter = 1;
					$before_last = $before_last.$last_letter;
				}
				
				for ($i=0;$i<sizeof($pieces);$i++) {
					$new_file_name .= ($i == sizeof($pieces)-2) ? $before_last.'.' : $pieces[$i].'.';
				}
				
				$this->file_new_name = substr($new_file_name, 0, -1);	
			}
		}
		
		/**
		* Upload the file
		*
		* @use $this->_file_
		* @use $this->destination_folder
		* @use $this->file_new_name
		* @use $this->sendOutput()
		*
		* @return bool
		*
		*/
		public function upload() {
			if (move_uploaded_file($this->_file_['tmp_name'], $this->destination_folder.$this->file_new_name)) {
				$this->sendOutput('File uploaded');
				return true;
			}
			else {
				$this->sendOutput('Error uploading the file');
				return false;
			}
		}
		
		/**
		* Return output
		*
		* @return string
		*
		*/
		public function sendOutput($output = null) {
			echo $output;
		}
	}