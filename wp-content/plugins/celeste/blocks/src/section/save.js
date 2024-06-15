import { InnerBlocks, useInnerBlocksProps, useBlockProps } from '@wordpress/block-editor';
import { generateClassName } from './functions';

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
	const hasBackgroundImage = attributes.mediaId != 0;
	const blockClassName = generateClassName(attributes);
	const sectionBlockProps = { className: blockClassName };

	if (hasBackgroundImage) {
		sectionBlockProps.style = {
			backgroundImage: `url(${attributes.mediaUrl})`,
		};
	}

	const TagName = attributes.tagName;
	const blockProps = useBlockProps.save(sectionBlockProps);
	const innerBlocksProps = useInnerBlocksProps.save({ className: 'section__content' });

	return (
		<TagName {...blockProps}>
			<div {...innerBlocksProps} />
		</TagName>
	);
}
