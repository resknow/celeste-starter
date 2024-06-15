import apiFetch from '@wordpress/api-fetch';
import { useBlockProps, InspectorControls, PlainText, RichText } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes, context }) {
	const { postId, queryId } = context;
	const [usePostId, setUsePostId] = useState(attributes.postId || postId);

	useEffect(() => {
		apiFetch({
			path: '/celeste/v1/blocks/custom-field',
			method: 'POST',
			data: {
				fieldName: attributes.fieldName,
				postId: usePostId,
			},
		}).then((response) => {
			setAttributes({
				fieldValue: response.fieldValue,
			});
		});
	}, [attributes.fieldName, attributes.postId, postId]);

	const blockProps = useBlockProps({
		className: 'custom-field',
		'data-field': attributes.fieldName,
		'data-post-id': usePostId,
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title="Custom Field">
					<TextControl
						label="Field Name"
						value={attributes.fieldName}
						onChange={(value) => {
							setAttributes({ fieldName: value });
						}}
						__nextHasNoMarginBottom
						help="Note: Only fields that return a string are supported."
					/>
					<TextControl
						label="Post ID"
						value={attributes.postId}
						onChange={(value) => {
							setAttributes({ postId: value });
							setUsePostId(value !== '' ? value : null);
						}}
						__nextHasNoMarginBottom
						help={`Set this to "option" to use global fields. Leave blank to use the current post ID: ${postId}`}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<RichText
					tagName="p"
					value={attributes.fieldValue}
					onChange={(value) => setAttributes({ fieldValue: attributes.fieldValue })}
					placeholder={'Select a field'}
					withoutInteractiveFormatting
					style={{ pointerEvents: 'none' }}
				/>
			</div>
		</>
	);
}
