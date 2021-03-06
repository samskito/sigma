<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',true);
ini_set('error_reporting',E_ALL);
$sigma_class = 'php/classes/sigma.class.php';
$settings_class = 'php/classes/settings.class.php';
$upload_class = 'php/classes/upload.class.php';

$path = $_SERVER['DOCUMENT_ROOT'].'/sigma/';
			
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Sigma test</title>
		<link href='http://fonts.googleapis.com/css?family=Lato:300,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" media="all" href="css/style.css" type="text/css" />
	</head>

	<body>
		<div id="container">
			<div id="menu">
			
				<?php
					if (file_exists($settings_class)) {
						require_once($settings_class);
					}
				?>
				
				<div id="menu_content">
					<!--<h2>Upload file</h2>-->
					<?php
						$settings = new Settings();
						echo '<input id="defaultGraph" value="'.$settings->getDefaultGraph().'" type="hidden" />';
						echo '<input id="path" value="'.$path.'" type="hidden" />';
						
						$uri = explode('?', $_SERVER['REQUEST_URI']);
						$uri = $uri[0];
						
						echo '<input id="site" value="'.$_SERVER['HTTP_HOST'].$uri.'" type="hidden" />';
					?>
					
					<h2>List of files</h2>
					<div class="form_menu">
					<select id="listOfGraphs">
					<?php
						$default_graph = str_replace('data/', '', $settings->getDefaultGraph());
						
						foreach($settings->getGraphs() as $graph) {
							echo '<option value="'.$graph.'" '.($default_graph == str_replace($path.'data/', '', $graph) ? 'selected' : '').'>';
							echo str_replace($path.'data/', '', $graph);
							echo '</option>';
						}
					?>
					</select>
					<button id="buttonOpenGraph">Open graph</button>
					</div>
					
					<?php
						if (file_exists($upload_class)) {
							require_once($upload_class);
						}
						
						if (isset($_FILES['gexf_file'])) {
							$handler = new Upload($path.'data/upload/', $_FILES['gexf_file'], 33554432, 'application/octet-stream', 'gexf', null, false);
							if ($handler->upload()) {
								$url = $_SERVER['PHP_SELF'];
								echo '<META http-equiv="refresh" content="0;URL='.$url.'">';	
							}
						}
					?>
					<h2>Upload a file</h2>
					<form action="#" method="post" enctype="multipart/form-data" class="form_menu">
					<input type="file" name="gexf_file" />
					<button>Upload GEXF</button>
					</form>
					
					<div id="closeMenu"></div>
				</div>
			</div>
			<?php
				$graph_name = str_replace($settings->getGraphLocation(), '', $settings->getDefaultGraph());
			?>
			<div id="graph_name"><?php echo $graph_name; ?></div>
			
			<div id="graph-container"></div>
			
			<div id="control-pane">
				<h2 class="underline">Settings</h2>
				<div>
					<strong>Search</strong><br/>
					<input type="text" value="" id="search_node_input" /><br/><br/>
					
					<strong>Public url</strong><br/>
					<input id="graph_url" value="<?php echo 'http://readidesignlab.lecolededesign.com/graph/?view='.$graph_name; ?>" type="text" />
				</div>
				<h2 class="underline">Filters</h2>
				
				<div>
					<h3>min degree <span id="min-degree-val">0</span></h3>
					0 <input id="min-degree" type="range" min="0" max="0" value="0"> <span id="max-degree-value">0</span><br>
				</div>
				
				<div>
					<h3>node category</h3>
					<select id="node-category">
						<option value="" selected>All categories</option>
					</select>
				</div>
			
				<span class="line"></span>
				
				<div>
					<button id="reset-btn">Reset filters</button>
					<button id="export-btn">Export</button>
				</div>
			
				<div id="dump" class="hidden"></div>
			</div>
		</div>
		
		
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
		<?php
			
			if (file_exists($sigma_class)) {
				// Include sigma class
				require_once($sigma_class);
				
				// Create object
				$sigma = new Sigma();
				
				// Load core + plugins
				$sigma::includeSigmaCore();
				
				// Load script File
				$sigma::start('script');
			}
			else {
				echo 'Error: Sigma class not found';
			}
		?>
	</body>
	
</html>