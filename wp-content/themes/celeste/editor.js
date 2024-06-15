/**
 * Unregister Core Block Styles
 */
function unregisterCoreBlockStyles() {
	wp.blocks.unregisterBlockStyle('core/image', ['rounded']);
}

/**
 * Prevent Blocks from parsing Alpine attributes
 */
function prevent_alpine_attribute_parsing(currentValue, nodeAttr) {
	if (nodeAttr.name.startsWith('x-') || nodeAttr.name.startsWith('@') || nodeAttr.name.startsWith(':')) {
		return { name: nodeAttr.name, value: nodeAttr.value };
	}

	return currentValue;
}

function prevent_select2_escaping_for_icon_select(escaped_value, original_value, $select, settings, field, instance) {
	return field.data.name.includes('icon') ? original_value : escaped_value;
}

wp.domReady(() => {
	unregisterCoreBlockStyles();
	acf.addFilter('acf_blocks_parse_node_attr', prevent_alpine_attribute_parsing);
	acf.addFilter('select2_escape_markup', prevent_select2_escaping_for_icon_select);
});
