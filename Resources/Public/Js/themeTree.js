/**
 * Created by markus on 27.10.13.
 */
jQuery(function(){
	console.log('test');

});

jQuery('.js_categoryBox').click(function(){
	// use closest and not parent
	// because user can modify dom with jQuery uniform
	var currentTreeDiv = jQuery(this).closest('div.themeTree');
	var currentLevel = 0;
	var treeDivClasses = currentTreeDiv.attr('class');
	var level = treeDivClasses.match(/(level)(\d)/);
	if (level.length == 3) {
		currentLevel = parseInt(level[2]);
	}

	var parentLevel = currentLevel - 1;

	var parentTree = jQuery(currentTreeDiv).closest('div.level' + parentLevel);

	var currentTreeDivState = jQuery(currentTreeDiv).find('.level' + currentLevel + ':checkbox').is(':checked');
	var currentId = parseInt(jQuery(this).val());
	if (currentTreeDivState === true) {
		if (currentId != '') {
			handelStack(currentId,'add');
		}
		checkParents(parentTree,parentLevel);
		checkChildren(currentTreeDiv);
	} else {
		if (currentId != '') {
			handelStack(currentId,'remove');
		}
		unCheckChildren(currentTreeDiv);
	}

	//	var siblings = currentTreeDiv.siblings();
//	console.log(siblings);
//	var allSiblingsChecked = true;
//	jQuery(siblings).each(function(){
//		if (!jQuery(this).find(':checkbox').is(':checked')) {
//			allSiblingsChecked = false;
//		}
//	});
//	console.log(allSiblingsChecked);

//	if (currentTreeDivState == false && allSiblingsChecked == false) {
//		console.log('remove parent');
//	}

	filterMarker();
});

/**
 * select all parents
 * @param parentTree
 * @param parentLevel
 */
function checkParents(parentTree,parentLevel) {
	parentTree.find('.level' + parentLevel + ':checkbox').attr('checked','checked');
	if (parentLevel > 0) {
		parentLevel--;
		var nextParent = jQuery(parentTree).closest('div.level' + parentLevel);
		checkParents(nextParent,parentLevel)
	}

}

/**
 * remove selection for all children
 * @param currentTreeDiv
 */
function unCheckChildren(currentTreeDiv) {
	currentTreeDiv.find('.children').find(':checkbox').attr('checked',false);
	currentTreeDiv.find('.children').find(':checkbox').each(function(){
		var currentId = parseInt(jQuery(this).val());
		handelStack(currentId,'remove');
	});
}
/**
 * select all children
 * @param currentTreeDiv
 */
function checkChildren(currentTreeDiv) {
	currentTreeDiv.find('.children').find(':checkbox').attr('checked',true);
	currentTreeDiv.find('.children').find(':checkbox').each(function(){
		var currentId = parseInt(jQuery(this).val());
		handelStack(currentId,'add');
	});
}

/**
 * add or remove id into filter stack for google map
 * @param currentId
 * @param addOrRemove
 */
function handelStack(currentId, addOrRemove) {
		if(addOrRemove == 'add') {
			if(selCats.indexOf(currentId) === -1 && !isNaN(currentId)) {
				selCats.push(currentId);
			}
		} else {
			while(selCats.indexOf(currentId) !== -1) {
				selCats.splice(selCats.indexOf(currentId), 1);
			}
		}
}