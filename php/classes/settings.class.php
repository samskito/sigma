<?php

	class Settings {
		private $default_graph = 'movies';
		private $extention = 'gexf';
		private $file_location = 'config/default_graph.txt';
		private $default_graph_location = 'data/';
		private $default_graph_uplaod_location = 'upload/';
		private $not_want = array('.', '..', 'upload');
		private $graph_list = array();
		private $path = '';
		
		function __construct() {
			$this->path = $_SERVER['DOCUMENT_ROOT'].'/sigma/';
			$this->makeGraphList();
			$this->default_graph = $this->readDefaultGraph();
		}
		
		private function readDefaultGraph() {
			if (isset($_GET['graph_name']) && trim($_GET['graph_name']) != '') {
				$requested_graph = $this->path.$this->default_graph_location.$_GET['graph_name'].'.'.$this->extention;
				
				if (in_array($requested_graph, $this->graph_list)) {
					return str_replace($this->path, '', $requested_graph);
				}
				else {
					return str_replace($this->path, '', $this->graph_list[0]);
				}
			}
			else {
				if (file_exists($this->path.$this->file_location)) {
					$file = file_get_contents($this->path.$this->file_location);
					return $file;	
				}	
			}
		}
		
		public function setDefaultGraph($data = null) {
			if (file_exists($this->path.$this->file_location)) {
				file_put_contents($this->path.$this->file_location, str_replace($this->path, '', $data));
			}
		}
		
		public function getDefaultGraph() {
			return $this->default_graph;
		}
		
		public function getGraphLocation() {
			return $this->default_graph_location;
		}
		
		private function makeGraphList() {
			$dir = scandir($this->path.$this->default_graph_location);
			$data_graphs = $this->cleanDirList($dir);
			
			$upload_dir = scandir($this->path.$this->default_graph_location.$this->default_graph_uplaod_location);
			$upload_graphs = $this->cleanDirList($upload_dir, true);
			
			$this->graph_list = array_merge($data_graphs, $upload_graphs);
		}
		
		private function cleanDirList($list = null, $upload = false) {
			if (is_array($list)) {
				$tmp = array();
				
				foreach ($list as $file) {
					$pieces_file = explode('.', $file);
					
					if (!in_array($file, $this->not_want) && end($pieces_file) == $this->extention) {
						array_push($tmp, $this->path.$this->default_graph_location.($upload ? $this->default_graph_uplaod_location : '').$file);
					}
				}
				
				return $tmp;
			}
		}
		
		public function getGraphs() {
			return $this->graph_list;
		}
	}