jQuery(function() {

	if(categoriesTree.length == 0) {
		return;
	}
	var categoryTree = jQuery('#categoryTree');
	categoryTree.tree({
		data: categoriesTree.children
	});
	categoryTree.bind(
		'tree.click',
		function(event) {
			selCats = [];
			selTags = [];
			selTypes = [];
			event.preventDefault();

			var selected_node = event.node;

				console.log(selected_node);
			if(categoryTree.tree('isNodeSelected', selected_node)) {
				categoryTree.tree('removeFromSelection', selected_node);
			}
			else {
				categoryTree.tree('addToSelection', selected_node);
				if (selected_node.id != null )
				selCats.push(selected_node.id);
			}

			recursiveHandlingOfNodes(categoryTree, selected_node);

			filterMarker();
			console.log(selCats);
			console.log(selTags);
			console.log(selTypes);
			console.log(listClusterMarkers);
		}
	);
});
function recursiveHandlingOfNodes(categoryTree, node) {
	var is_selected = categoryTree.tree('isNodeSelected', node);
	if(is_selected) {
		for(var i = 0; i < node.children.length; i++) {
			var child = node.children[i];
			if (child.id != null ) {
				selTags.push(child.id);
			} else {
				selTypes.push(child.name);
			}
			categoryTree.tree('addToSelection', child);
			recursiveHandlingOfNodes(categoryTree, child);
		}
	} else {
		for(var i = 0; i < node.children.length; i++) {
			var child = node.children[i];
			categoryTree.tree('removeFromSelection', child);
			recursiveHandlingOfNodes(categoryTree, child);
		}

	}

}