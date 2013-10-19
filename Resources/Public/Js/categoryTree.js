var data = [
	{
		label: 'category1',
		children: [
			{ label: 'test1', id: 11 },
			{ label: 'test2', id: 12 },
			{ label: 'test3', id: 13 },
			{ label: 'test4', id: 14, children: [
				{ label: 'test41', id: 21},
				{ label: 'test42', id: 22},
				{ label: 'test43', id: 23},
				{ label: 'test44', id: 24}
			] }
		]
	},
	{
		label: 'category2',
		children: [
			{ label: 'child3' }
		]
	}
];
//console.log(categoriesTree.children);
//console.log(data);
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
			event.preventDefault();

			var selected_node = event.node;

			if(categoryTree.tree('isNodeSelected', selected_node)) {
				categoryTree.tree('removeFromSelection', selected_node);
			}
			else {
				categoryTree.tree('addToSelection', selected_node);
			}
			recursiveHandlingOfNodes(categoryTree, selected_node);

		}
	);
});
function recursiveHandlingOfNodes(categoryTree,node) {
	var is_selected = categoryTree.tree('isNodeSelected', node);
	if(is_selected) {
		for(var i = 0; i < node.children.length; i++) {
			var child = node.children[i];
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