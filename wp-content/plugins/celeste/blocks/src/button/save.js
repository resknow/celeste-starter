import { RichText, useBlockProps } from '@wordpress/block-editor';

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
	const blockProps = useBlockProps.save();

	/**
	 * Create a list of types and their corrosponding HTML elements
	 */
	const types = {
		link: 'a',
		button: 'button',
		submit: 'button',
	};

	const TagName = types[attributes.type || 'button'];

	return (
		<div {...blockProps}>
			<TagName
				className="btn"
				data-variant={attributes.variant}
				data-size={attributes.size}
				data-icon-position={attributes.iconPosition}
				type={attributes.type !== 'link' ? attributes.type : null}
				href={attributes.type === 'link' ? attributes.href : null}
			>
				{attributes.icon && '[icon]'}
				<RichText.Content tagName="span" value={attributes.text} />
			</TagName>
		</div>
	);
}
