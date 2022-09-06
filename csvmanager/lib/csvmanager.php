<?php

class CSVManager {
	public $current_file;
	public $files_in_dir;
	public $sort_by_timestamp = false;
	
	public function __construct($file, $sort_by_timestamp=false)
    {
		if($sort_by_timestamp) {
			$this->sort_by_timestamp = $sort_by_timestamp;
		}
		if(is_file($file)) {
			$this->current_file = $file;
		} else {
			echo "error: file does not exists<br>";
			echo "file: $file";
		}
		$this->get_csv_files(dirname($file));
	}
	
	public function get_csv_files($directory) {
		$dir_content = scandir(getcwd()."/../wp-content/plugins/csvmanager/uploads/");
		$csv_files = array();
		foreach($dir_content as $entry) {
			if($entry != "." && $entry != ".." && str_ends_with($entry, ".csv")) {
				$mtime_ = filemtime(getcwd()."/../wp-content/plugins/csvmanager/uploads/".$entry);
				$file_array = array(
					"file" => $entry,
					"mtime" => $mtime_
				);
				$csv_files[] = $file_array;
			}
		}
		if(count($csv_files) > 0) {
			if($this->sort_by_timestamp == true) {
				$columns_1 = array_column($csv_files, 'mtime');
				array_multisort($columns_1, SORT_ASC, $csv_files); // $columns_2, SORT_DESC	
			}
			$this->files_in_dir = $csv_files;
		}
	}
	
	public function get_csv_header() {
		$rows = array();
		$row = 1;
		if (($handle = fopen($this->current_file, "r")) !== FALSE) {
		  while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
			if($row == 1) {
				$num = count($data);
				echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
				for ($c=0; $c < $num; $c++) {
					$rows[] = $data[$c];
				}
			}
		  }
		  fclose($handle);
		}
		return $rows;
	}
}