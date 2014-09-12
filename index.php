<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',true);
ini_set('error_reporting',E_ALL);
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
			<div id="graph-container"></div>
			
			<div id="control-pane">
				<h2 class="underline">filters</h2>
				
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
			
			$sigma_class = 'php/classes/sigma.class.php';
			
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