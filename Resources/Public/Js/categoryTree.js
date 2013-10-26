jQuery(function() {
	//@todo: wird eine node gewählt, müssen alle childs selektiert werden, sowie die parent nodes recursiv
	if(categoriesTree.length == 0) {
		return;
	}

	var categoryTree = jQuery('#categoryTree');
	categoryTree.tree({
		data: [categoriesTree]
	});

	categoryTree.bind(
		'tree.click',
		function(event) {
			var eventNode = event.node;
			console.log(categoryTree.tree('isNodeSelected', eventNode));
			event.preventDefault();
			var selected = categoryTree.tree('isNodeSelected', eventNode);

			if(selected) {
				// current not is selected, remove selection now
				handelChildren(eventNode, 'remove');
				handelStack(eventNode, 'remove');

				// workaround for jqTree?:
				var isParentSelected = categoryTree.tree('isNodeSelected', eventNode.parent);
				// if removeFromSelection() will be used on node, the parent note lost selection: bug?
				console.log('test1: ' + categoryTree.tree('isNodeSelected', eventNode.parent));
				categoryTree.tree('removeFromSelection', eventNode);
				console.log('test2: ' + categoryTree.tree('isNodeSelected', eventNode.parent));
				if (isParentSelected) {
					categoryTree.tree('addToSelection', eventNode.parent);
				}
				console.log('test3: ' + categoryTree.tree('isNodeSelected', eventNode.parent));

				checkForSelectedSiblings(eventNode);
			} else {
				// current not is not selected, add selection now
				addParents(eventNode); // jqTree bug: first sibling of current node will be selected to! why...?
//				checkForSelectedSiblings(eventNode,true);
				handelStack(eventNode, 'add');
//				categoryTree.tree('addToSelection', eventNode);
				handelChildren(eventNode, 'add');
			}
			console.log(selCats);
			filterMarker();
		}
	);

	/**
	 * search for active sibling.
	 * if no active sibling found, remove parent node selection
	 *
	 * @param node
	 * @param debugMode
	 */
	function checkForSelectedSiblings(node,debugMode) {
		var foundActiveSiblings = false;
		// loop all sibling to search for a active sibling
		jQuery(node.parent.children).each(function(loop, siblingNode) {
			var selected = categoryTree.tree('isNodeSelected', siblingNode);
			if(selected) {
				foundActiveSiblings = true;
			}
			if (debugMode !== undefined) {
				console.log(siblingNode.name + ': ' + selected);
			}
		});
		var selected = categoryTree.tree('isNodeSelected', node.parent);
		console.log(selected);

		// no sibling found, remove parent selection
		if(!foundActiveSiblings) {
			handelStack(node.parent, 'remove');
			categoryTree.tree('removeFromSelection', node.parent);

			if(node.parent.getLevel() > 1) {
				// check for parent has active sibling
				checkForSelectedSiblings(node.parent);
			}
		}

	}

	/**
	 * add or remove children fields
	 * @param node
	 * @param addOrRemove
	 */
	function handelChildren(node, addOrRemove) {
		jQuery(node.children).each(function(loop, child) {
			if(addOrRemove == 'add') {
				categoryTree.tree('addToSelection', child);
			} else {
				categoryTree.tree('removeFromSelection', child);
			}
			handelStack(child, addOrRemove);
			if(child.children.length > 0) {
				handelChildren(child, addOrRemove);
			}
		});
	}

	/**
	 * select all parents
	 * @param node
	 */
	function addParents(node) {
		if(node.parent == null) {
			return;
		}

		categoryTree.tree('addToSelection', node.parent);
		handelStack(node.parent, 'add');
		addParents(node.parent);

	}

	/**
	 * add or remove id into filter stack for google map
	 * @param node
	 * @param addOrRemove
	 */
	function handelStack(node, addOrRemove) {
		if(node.getLevel() == 3) {
			if(addOrRemove == 'add') {
				if(selCats.indexOf(node.id) === -1 && node.id !== null) {
					selCats.push(node.id);
				}
			} else {
				while(selCats.indexOf(node.id) !== -1) {
					selCats.splice(selCats.indexOf(node.id), 1);
				}
			}
		}
	}

});