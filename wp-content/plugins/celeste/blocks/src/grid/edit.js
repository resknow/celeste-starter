import {
	BlockControls,
	BlockVerticalAlignmentControl,
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { BaseControl, Button, ButtonGroup, PanelBody, RangeControl } from '@wordpress/components';
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
	const { children, ...combinedBlockProps } = useInnerBlocksProps(blockProps, {
		allowedBlocks: ['resknow/grid-item'],
		orientation: 'horizontal',
	});

	return (
		<>
			<BlockControls group="block">
				<BlockVerticalAlignmentControl
					onChange={(value) => setAttributes({ verticalAlignment: value })}
					value={attributes.verticalAlignment}
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody title="Appearance">
					<RangeControl
						allowReset={true}
						help="How many columns should be displayed?"
						label="Columns"
						value={attributes.columns}
						onChange={(value) => setAttributes({ columns: value })}
						min="1"
						max="6"
					/>
					<BaseControl label="Gap" help="The amount of space between each grid item">
						<div>
							<ButtonGroup>
								<Button
									variant={attributes.gap === 'sm' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ gap: 'sm' })}
									isSmall
								>
									Small
								</Button>
								<Button
									variant={attributes.gap === 'md' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ gap: 'md' })}
									isSmall
								>
									Medium
								</Button>
								<Button
									variant={attributes.gap === 'lg' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ gap: 'lg' })}
									isSmall
								>
									Large
								</Button>
								<Button
									variant={attributes.gap === 'xl' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ gap: 'xl' })}
									isSmall
								>
									X-Large
								</Button>
							</ButtonGroup>
						</div>
					</BaseControl>
				</PanelBody>
				<PanelBody title="Responsive" initialOpen={false}>
					<RangeControl
						allowReset={true}
						help="How many columns should be displayed on tablet/medium devices?"
						label="Tablet Columns"
						value={attributes.columnsMd}
						onChange={(value) => setAttributes({ columnsMd: value })}
						min="1"
						max="6"
					/>
					<RangeControl
						allowReset={true}
						help="How many columns should be displayed on desktop/large devices?"
						label="Desktop Columns"
						value={attributes.columnsLg}
						onChange={(value) => setAttributes({ columnsLg: value })}
						min="1"
						max="6"
					/>
				</PanelBody>
			</InspectorControls>
			<div {...combinedBlockProps}>{children}</div>
		</>
	);
}
