import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { generateClassName } from './functions';

import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const blockClassName = generateClassName(attributes);
	const blockProps = useBlockProps({ className: blockClassName });
	const { children, ...combinedBlockProps } = useInnerBlocksProps(blockProps);
	return (
		<>
			<InspectorControls>
				<PanelBody title="Appearance">
					<RangeControl
						allowReset={true}
						help="How many columns should this content span?"
						label="Column Span"
						value={attributes.span}
						onChange={(value) => setAttributes({ span: value })}
						min="1"
						max={attributes.maxColumns}
					/>
					<RangeControl
						allowReset={true}
						help="Which column should this content start at?"
						label="Start at"
						value={attributes.start}
						onChange={(value) => setAttributes({ start: value })}
						min="1"
						max={attributes.maxColumns - 1}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...combinedBlockProps}>{children}</div>
		</>
	);
}
