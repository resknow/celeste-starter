import { InnerBlocks, useInnerBlocksProps, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { generateClassName } from './functions';

import './editor.scss';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save({ attributes }) {
	const blockClassName = generateClassName(attributes);
	const blockProps = useBlockProps.save({ className: blockClassName });
	const innerBlocksProps = useInnerBlocksProps.save(blockProps);

	return <div {...innerBlocksProps} />;
}
