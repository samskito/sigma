<?php

	class Sigma {
		static private $core = array( 'sigma.core',
									  'conrad',
									  'utils/sigma.utils',
									  'utils/sigma.polyfills',
									  'sigma.settings',
									  'classes/sigma.classes.dispatcher',
									  'classes/sigma.classes.configurable',
									  'classes/sigma.classes.graph',
									  'classes/sigma.classes.camera',
									  'classes/sigma.classes.quad',
									  'captors/sigma.captors.mouse',
									  'captors/sigma.captors.touch',
									  'renderers/sigma.renderers.canvas',
									  'renderers/sigma.renderers.webgl',
									  'renderers/sigma.renderers.def',
									  'renderers/webgl/sigma.webgl.nodes.def',
									  'renderers/webgl/sigma.webgl.nodes.fast',
									  'renderers/webgl/sigma.webgl.edges.def',
									  'renderers/webgl/sigma.webgl.edges.fast',
									  'renderers/webgl/sigma.webgl.edges.arrow',
									  'renderers/canvas/sigma.canvas.labels.def',
									  'renderers/canvas/sigma.canvas.hovers.def',
									  'renderers/canvas/sigma.canvas.nodes.def',
									  'renderers/canvas/sigma.canvas.edges.def',
									  'renderers/canvas/sigma.canvas.edges.curve',
									  'renderers/canvas/sigma.canvas.edges.arrow',
									  'renderers/canvas/sigma.canvas.edges.curvedArrow',
									  'middlewares/sigma.middlewares.rescale',
									  'middlewares/sigma.middlewares.copy',
									  'misc/sigma.misc.animation',
									  'misc/sigma.misc.bindEvents',
									  'misc/sigma.misc.drawHovers');
		static private $plugins = array( 'sigma.parsers.gexf/gexf-parser',
									 	 'sigma.parsers.gexf/sigma.parsers.gexf',
									 	 'sigma.plugins.filter/sigma.plugins.filter');
		static private $sigma_location = 'sigma/';
		static private $sigma_core_location = 'sigma_core/';
		static private $sigma_plugins_location = 'plugins/';
		
		static public function includeSigmaCore() {
			$output = '';
			
			foreach (self::$core as $core_file) {
				$core_file = self::$sigma_location.self::$sigma_core_location.$core_file.'.js';
				
				if (file_exists($core_file)) {
					$output .= '<script type="text/javascript" src="'.$core_file.'"></script>';
				}
			}
			
			foreach (self::$plugins as $plugin_file) {
				$plugin_file = self::$sigma_location.self::$sigma_plugins_location.$plugin_file.'.js';
				
				if (file_exists($core_file)) {
					$output .= '<script type="text/javascript" src="'.$plugin_file.'"></script>';
				}
			}
			
			echo $output;
		}
		
		static public function start($script_name = NULL) {
			if (file_exists('js/'.$script_name.'.js')) {
				echo '<script type="text/javascript" src="js/'.$script_name.'.js"></script>';
			}
			else {
				echo 'Error: script file not found';
			}
		}
	}