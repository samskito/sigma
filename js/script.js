$(document).ready(function(){
	var filter;
	
	/**
	 * DOM utility functions
	 */
	var _ = {
	  $: function (id) {
	    return document.getElementById(id);
	  },
	
	  all: function (selectors) {
	    return document.querySelectorAll(selectors);
	  },
	
	  removeClass: function(selectors, cssClass) {
	    var nodes = document.querySelectorAll(selectors);
	    var l = nodes.length;
	    for ( i = 0 ; i < l; i++ ) {
	      var el = nodes[i];
	      // Bootstrap compatibility
	      el.className = el.className.replace(cssClass, '');
	    }
	  },
	
	  addClass: function (selectors, cssClass) {
	    var nodes = document.querySelectorAll(selectors);
	    var l = nodes.length;
	    for ( i = 0 ; i < l; i++ ) {
	      var el = nodes[i];
	      // Bootstrap compatibility
	      if (-1 == el.className.indexOf(cssClass)) {
	        el.className += ' ' + cssClass;
	      }
	    }
	  },
	
	  show: function (selectors) {
	    this.removeClass(selectors, 'hidden');
	  },
	
	  hide: function (selectors) {
	    this.addClass(selectors, 'hidden');
	  },
	
	  toggle: function (selectors, cssClass) {
	    var cssClass = cssClass || "hidden";
	    var nodes = document.querySelectorAll(selectors);
	    var l = nodes.length;
	    for ( i = 0 ; i < l; i++ ) {
	      var el = nodes[i];
	      //el.style.display = (el.style.display != 'none' ? 'none' : '' );
	      // Bootstrap compatibility
	      if (-1 !== el.className.indexOf(cssClass)) {
	        el.className = el.className.replace(cssClass, '');
	      } else {
	        el.className += ' ' + cssClass;
	      }
	    }
	  }
	};
	
	// Add method neighbors
	sigma.classes.graph.addMethod('neighbors', function(nodeId) {
	    var k,
	        neighbors = {},
	        index = this.allNeighborsIndex[nodeId] || {};
	
	    for (k in index)
	      neighbors[k] = this.nodesIndex[k];
	
	    return neighbors;
	  });
	// End addMethod
	
	function updatePane (graph, filter) {
	  // get max degree
	  var maxDegree = 0,
	      categories = {};
	  
	  // read nodes
	  graph.nodes().forEach(function(n) {
	    maxDegree = Math.max(maxDegree, graph.degree(n.id));
	    categories[n.attributes.acategory] = true;
	  })
	
	  // min degree
	  _.$('min-degree').max = maxDegree;
	  _.$('max-degree-value').textContent = maxDegree;
	  
	  // node category
	  var nodecategoryElt = _.$('node-category');
	  Object.keys(categories).forEach(function(c) {
	    var optionElt = document.createElement("option");
	    optionElt.text = c;
	    nodecategoryElt.add(optionElt);
	  });
	
	  // reset button
	  _.$('reset-btn').addEventListener("click", function(e) {
	    _.$('min-degree').value = 0;
	    _.$('min-degree-val').textContent = '0';
	    _.$('node-category').selectedIndex = 0;
	    filter.undo().apply();
	    _.$('dump').textContent = '';
	    _.hide('#dump');
	  });
	
	  // export button
	  _.$('export-btn').addEventListener("click", function(e) {
	    var chain = filter.export();
	    console.log(chain);
	    _.$('dump').textContent = JSON.stringify(chain);
	    _.show('#dump');
	  });
	}
	
	// Initialize sigma with the dataset:
	var defaultGraph = $('#defaultGraph').val();
	console.log(defaultGraph);
	
	sigma.parsers.gexf(defaultGraph, {
	  container: 'graph-container',
	  settings: {
	    edgeColor: 'default',
	    defaultEdgeColor: '#ccc',
	    edgeLabels: 'flase'
	  }
	}, function(s) {
	  // Initialize the Filter API
	  filter = new sigma.plugins.filter(s);
	
	  updatePane(s.graph, filter);
	
	  function applyMinDegreeFilter(e) {
	    var v = e.target.value;
	    _.$('min-degree-val').textContent = v;
	
	    filter
	      .undo('min-degree')
	      .nodesBy(function(n) {
	        return this.degree(n.id) >= v;
	      }, 'min-degree')
	      .apply();
	  }
	
	  function applyCategoryFilter(e) {
	    var c = e.target[e.target.selectedIndex].value;
	    filter
	      .undo('node-category')
	      .nodesBy(function(n) {
	        return !c.length || n.attributes.acategory === c;
	      }, 'node-category')
	      .apply();
	  }
	  
	  // ADDED BY GEOFFROY
	  function applySearch(e) {
	  	  var search = $('#search_node_input').val();
	  	  
		  if (search.length == 0) {
		  	  filter.undo('node-name');
		  }
		  else {
		  	  filter.undo('node-name').nodesBy(function(e){
				  //console.log(e);
				  return e.label.toLowerCase().indexOf(search.toLowerCase()) > -1;
			  }, 'node-name').apply();
		  }
	  }
	
	  _.$('min-degree').addEventListener("input", applyMinDegreeFilter);  // for Chrome and FF
	  _.$('min-degree').addEventListener("change", applyMinDegreeFilter); // for IE10+, that sucks
	  _.$('node-category').addEventListener("change", applyCategoryFilter);
	  _.$('search_node_input').addEventListener("keyup", applySearch); // ADDED BY GEOFFROY
	  
	  s.graph.nodes().forEach(function(n) {
        n.originalColor = n.color;
      });
      s.graph.edges().forEach(function(e) {
        e.originalColor = e.color;
      });
      
      s.bind('clickNode', function(e) {
        var nodeId = e.data.node.id,
            toKeep = s.graph.neighbors(nodeId);
        toKeep[nodeId] = e.data.node;
        
        filter.undo('node-name').apply(); // ADDED BY GEOFFROY
        
        s.graph.nodes().forEach(function(n) {
          if (toKeep[n.id])
            n.color = n.originalColor;
          else
            n.color = '#eee';
        });

        s.graph.edges().forEach(function(e) {
          if (toKeep[e.source] && toKeep[e.target])
            e.color = e.originalColor;
          else
            e.color = '#eee';
        });

        // Since the data has been modified, we need to
        // call the refresh method to make the colors
        // update effective.
        s.refresh();
      });

      // When the stage is clicked, we just color each
      // node and edge with its original color.
      s.bind('clickStage', function(e) {
        s.graph.nodes().forEach(function(n) {
          n.color = n.originalColor;
        });

        s.graph.edges().forEach(function(e) {
          e.color = e.originalColor;
        });

        // Same as in the previous event:
        s.refresh();
      });
		      
	}); // End of callback function in parsers.gexf

	/////////////////////////////
	// Menu
	var theMenu = '#menu';
	$(theMenu).bind('click', function(e){
		var _this = $(this);
		if (_this.hasClass('menu_openned')) {
			// Close
			//_this.removeClass('menu_openned');
			//iconMenuCss();
		}
		else {
			// Open
			_this.addClass('menu_openned');
			wideMenuCss();
		}
	});
	
	function wideMenuCss() {
		$(theMenu).css('background-image', 'none');
		$(theMenu).animate({
			'width': '250px',
			'height': '260px',
			'padding': '10px',
			backgroundImage: 'none'
		}, 500, '', function() {
			$('#menu_content').show();
			$(theMenu).css({
				'cursor': 'default'
			});
		});
	}
	
	function iconMenuCss() {
		$('#menu_content').hide();
		$(theMenu).animate({
			'width': '40px',
			'height': '40px',
			'padding': '0px'
		}, 500,'',function() {
			$(theMenu).css({
				'background-image': 'url(img/menu.png)',
				'cursor': 'pointer'
			});
		});
	}
	
	$('#buttonOpenGraph').on('click', function() {
		var newGraph = $('#listOfGraphs').val();
		var path = $('#path').val();
		var site = $('#site').val();
		
		$.ajax({
			type: 'POST',
			url: 'php/select_graph.php',
			data: {graph: newGraph},
			success: function(data) {
				var param = newGraph.replace(path+'data/', '').replace('.gexf', '');
				var test = 'http://' + site + '?graph_name=' + param;
				
				//location.reload();
				console.log(test);
				document.location = test;
			}
		});
	});
	
	$('#closeMenu').on('click', function(e) {
		$(theMenu).removeClass('menu_openned');
		iconMenuCss();
		e.stopPropagation();
	});
	
	var width_graph_name_div = '-' + ($('#graph_name').width() / 2) + 'px';
	$('#graph_name').css({
		'left': '50%',
		'margin-left': width_graph_name_div
	});
	// End Menu
	/////////////////////////////
});

// WORKS SIMPLE GRAPH 
/*sigma.parsers.gexf(
'TEST.gexf',
{
container: 'sigma-container'
},function(s){}
);*/