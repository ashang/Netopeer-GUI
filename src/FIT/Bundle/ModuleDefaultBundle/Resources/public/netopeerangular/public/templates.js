angular.module("configurationTemplates", []).run(["$templateCache", function($templateCache) {$templateCache.put("types/Array.html","<i ng-click=\"toggleCollapse()\" class=\"fa toggle-control\" ng-class=\"chevron\"></i>\n<div class=\"jsonContents\" ng-hide=\"collapsed\">\n	<!-- TODO: sortable only if user-sorted = autohor -->\n	<ol class=\"arrayOl\" ui-sortable=\"sortableOptions\" ng-model=\"child\">\n		<li class=\"arrayItem\" ng-repeat=\"val in child track by $index\">\n			<span><switch-item></switch-item></span>\n		</li>\n	</ol>\n	<add-item ng-show=\"jsonEditable\" valueType=\"Text\"></add-item>\n</div>");
$templateCache.put("types/List.html","<i ng-click=\"toggleCollapse()\" class=\"fa toggle-control\" ng-class=\"chevron\"></i>\n<div class=\"jsonContents\" ng-hide=\"collapsed\">\n	<!-- TODO: sortable only if user-sorted = autohor -->\n	<ol class=\"listOl\" ui-sortable=\"sortableOptions\" ng-model=\"child\">\n		<li class=\"arrayItem\" ng-repeat=\"val in child track by $index\">\n			<span><switch-item></switch-item></span>\n		</li>\n	</ol>\n	<add-item ng-show=\"jsonEditable\" valueType=\"Object\"></add-item>\n</div>");
$templateCache.put("types/Object.html","<i ng-click=\"toggleCollapse()\" class=\"fa toggle-control\" ng-class=\"chevron\" ng-if=\"!hideCollapse\"></i>\n<div class=\"jsonContents\" ng-hide=\"collapsed\">\n	<span class=\"block\" ng-repeat=\"(key, val) in child | skipAttributes\" ng-if=\"isVisible(key, child, $parent)\"> <!--ng-class=\"contentStatus\"-->\n		<span ng-class=\"{\'jsonObjectKey\': 1, \'objectOrArrayCover\': isObjectOrArray(key, val, child)}\">\n			<span class=\"key-name\" ng-init=\"newkey=key\">\n				{{ key }}\n				<span class=\"editbar\">\n					<i class=\"iconButton fa fa-question-circle\" tooltip-placement=\"top\" uib-tooltip=\"{{child[\'$@\'+key][\'description\']}}\" ng-if=\"child[\'$@\'+key][\'description\']\"></i>\n					<i class=\"iconButton fa fa-asterisk red\" tooltip-placement=\"top\" uib-tooltip=\"Mandatory\" ng-if=\"isMandatory(key, child)\"></i>\n					<i class=\"iconButton deleteKeyBtn fa fa-trash\" ng-click=\"deleteKey(key, child, $parent)\" ng-if=\"!isMandatory(key, child)\"></i>\n				</span>\n			</span>\n			<!--<input class=\"keyinput\" type=\"text\" ng-model=\"newkey\" ng-init=\"newkey=key\" ng-blur=\"moveKey(child, key, newkey)\"/>-->\n		</span>\n		<span class=\"jsonObjectValue\">\n			<switch-item></switch-item>\n		</span>\n	</span>\n	<add-item ng-show=\"jsonEditable\"></add-item>\n</div>");
$templateCache.put("directives/addItem.html","<span ng-if=\"showAddKey && jsonEditable\">\n\n	<span ng-show=\"$parent.type !== \'List\' && $parent.type !== \'Array\'\">\n		<input placeholder=\"Name\" type=\"text\"\n					 class=\"form-control input-sm addItemKeyInput\" ng-model=\"$parent.keyName\" autocomplete=\"off\" uib-typeahead=\"subchild for subchild in getAvailableNodeNames(key, child, $parent)\" 					 ng-blur=\"changeParentKeyName($parent.keyName, child, $parent)\"\n				/> :\n	</span>\n\n	<select ng-model=\"$parent.valueType\"  ng-model-onblur ng-options=\"option for option in valueTypes\" class=\"form-control input-sm\" ng-show=\"$parent.type !== \'List\' && $parent.type !== \'Array\'\" ng-init=\"initParentValueType($parent)\"></select>\n\n	<!--<input type=\"text\" ng-model=\"$parent.valueName\" placeholder=\"Empty\" ng-model-onblur-->\n				 <!--ng-show=\"$parent.valueType !== \'Object\' && $parent.valueType !== \'Array\' && $parent.valueType !== \'string:Object\' && $parent.valueType !== \'string:Array\' && $parent.type !== \'List\'\">-->\n\n\n<span ng-switch on=\"$parent.valueType\" ng-show=\"$parent.valueType !== \'Object\' && $parent.valueType !== \'Array\' && $parent.valueType !== \'string:Object\' && $parent.valueType !== \'string:Array\' && $parent.type !== \'List\'\">\n	<span ng-switch-when=\"Boolean\">\n		<input type=\"checkbox\" ng-model=\"$parent.valueName\" ng-model-onblur>\n	</span>\n	<span ng-switch-when=\"Enumeration\">\n		<select ng-model=\"$parent.valueName\" ng-model-onblur ng-options=\"option for option in getEnumValues(key, child, $parent) track by option\"></select>\n	</span>\n	<span ng-switch-when=\"Number\">\n		<input type=\"number\" ng-model=\"$parent.valueName\" ng-model-onblur>\n	</span>\n	<span ng-switch-default>\n		<input type=\"text\" ng-model=\"$parent.valueName\" placeholder=\"Empty\" ng-model-onblur>\n	</span>\n</span>\n	<!--<span ng-switch on=\"$parent.valueType\">-->\n		<!--<span ng-switch-when=\"Boolean\" type=\"boolean\">-->\n			<!--<input type=\"checkbox\" ng-model=\"$parent.valueName\" ng-model-onblur ng-change=\"changeValue(val, $parent.keyName, child, $parent)\">-->\n		<!--</span>-->\n		<!--<span ng-switch-when=\"Enumeration\" type=\"enumeration\">-->\n			<!--<select ng-model=\"$parent.valueName\" ng-model-onblur ng-change=\"changeValue(val, $parent.keyName, child, $parent)\" ng-options=\"option for option in getEnumValues($parent.keyName, child, $parent) track by option\"></select>-->\n		<!--</span>-->\n		<!--<span ng-switch-when=\"Number\" type=\"number\">-->\n			<!--<input type=\"number\" ng-model=\"$parent.valueName\" ng-model-onblur ng-change=\"changeValue(val, $parent.keyName, child, $parent)\">-->\n		<!--</span>-->\n		<!--<span ng-switch-default class=\"jsonLiteral\" type=\"text\">-->\n			<!--<input type=\"text\" ng-model=\"$parent.valueName\" placeholder=\"Empty\" ng-model-onblur-->\n						 <!--ng-change=\"changeValue($parent.valueName, $parent.keyName, child, $parent)\" ng-show=\"$parent.valueType != \'Object\' && $parent.valueType != \'Array\'\">-->\n		<!--</span>-->\n	<!--</span>-->\n\n	<button class=\"btn btn-primary btn-sm\" ng-click=\"addItem(key, child, $parent)\">\n		Add\n	</button>\n	<button class=\"btn btn-default btn-sm\" ng-click=\"$parent.showAddKey=false\">\n		Cancel\n	</button>\n</span>\n<span ng-if=\"!showAddKey\">\n	<button class=\"addObjectItemBtn\" ng-click=\"$parent.showAddKey = true\">\n		<i class=\"fa fa-plus\" ng-if=\"$parent.type !== \'List\'\"></i>\n		<i class=\"fa fa-plus-circle\" ng-if=\"$parent.type === \'List\' \"></i>\n	</button>\n</span>");
$templateCache.put("directives/switchItem.html","<span ng-switch on=\"getType(key, val, child)\">\n	<json ng-switch-when=\"Object\" child=\"val\" type=\"Object\" default-collapsed=\"defaultCollapsed\"></json>\n	<json ng-switch-when=\"Array\" child=\"val\" type=\"Array\" default-collapsed=\"defaultCollapsed\"></json>\n	<json ng-switch-when=\"List\" child=\"val\" type=\"List\" default-collapsed=\"defaultCollapsed\"></json>\n\n	<span ng-switch-when=\"Boolean\">\n		<input type=\"checkbox\" ng-model=\"val\" ng-model-onblur ng-change=\"changeValue(val, key, child, $parent)\" ng-if=\"isConfig(key, child)\" ng-disabled=\"isKey(key, child)\">\n		<span class=\"val\" ng-if=\"!isConfig(key, child)\">{{ val }}</span>\n	</span>\n	<span ng-switch-when=\"Enumeration\">\n		<select ng-model=\"val\" ng-model-onblur ng-change=\"changeValue(val, key, child, $parent)\" ng-if=\"isConfig(key, child)\" ng-disabled=\"isKey(key, child)\" ng-options=\"option for option in getEnumValues(key, child, $parent) track by option\"></select>\n		<span class=\"val\" ng-if=\"!isConfig(key, child)\">{{ val }}</span>\n	</span>\n	<span ng-switch-when=\"Number\">\n		<input type=\"number\" ng-model=\"val\" ng-model-onblur ng-change=\"changeValue(val, key, child, $parent)\" ng-if=\"isConfig(key, child)\" ng-disabled=\"isKey(key, child)\">\n		<span class=\"val\" ng-if=\"!isConfig(key, child)\">{{ val }}</span>\n	</span>\n	<span ng-switch-default class=\"jsonLiteral\">\n		<input type=\"text\" ng-model=\"val\" placeholder=\"Empty\" ng-model-onblur ng-change=\"changeValue(val, key, child, $parent)\"  ng-if=\"isConfig(key, child)\" ng-disabled=\"isKey(key, child)\">\n		<span class=\"val\" ng-if=\"!isConfig(key, child)\">{{ val }}</span>\n	</span>\n\n	<!--<add-item ng-show=\"jsonEditable && !val.length\"></add-item>-->\n</span>");
$templateCache.put("main/view.html","<h1 class=\"left\">Config &amp; State data</h1>\n\n<hr class=\"cleaner\" />\n<div ng-init=\"reloadData(moduleName); resetRevisions()\">\n    <button ng-click=\"reloadData(moduleName)\">reload data</button>\n    <button ng-click=\"download(jsonData)\">download as JSON</button>\n\n    <button ng-click=\"submitConfiguration(jsonData)\" ng-if=\"jsonEditable\">Apply changes</button>\n    <button ng-click=\"undo()\" ng-disabled=\"hasUndo()\" ng-if=\"jsonEditable\">history undo</button>\n    <button ng-click=\"redo()\" ng-disabled=\"hasRedo()\" ng-if=\"jsonEditable\">history redo</button>\n\n    <div class=\"jsonView\">\n        <json child=\"jsonData\" default-collapsed=\"false\" type=\"object\" hide-collapse=\"true\"></json>\n    </div>\n\n    <!--<hr>-->\n    <!--<div>-->\n        <!--<textarea id=\"jsonTextarea\" ng-model=\"jsonString\"></textarea>-->\n        <!--<span class=\"red\" ng-if=\"!wellFormed\">JSON not well-formed!</span>-->\n    <!--</div>-->\n</div>");}]);