import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	Button,
	Flex,
	FlexItem,
	PanelBody,
	ResponsiveWrapper,
	SelectControl,
	ToggleControl,
	__experimentalSpacer as Spacer,
} from '@wordpress/components';
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
export default function Edit({ attributes, setAttributes, media }) {
	const hasBackgroundImage = attributes.mediaId != 0;
	const blockClassName = generateClassName(attributes);
	const sectionBlockProps = { className: blockClassName };

	if (hasBackgroundImage) {
		sectionBlockProps.style = {
			backgroundImage: `url(${attributes.mediaUrl})`,
		};
	}

	const TagName = attributes.tagName;
	const blockProps = useBlockProps(sectionBlockProps);
	const innerBlockProps = useInnerBlocksProps({ className: 'section__content' });

	const removeMedia = () => {
		setAttributes({
			mediaId: 0,
			mediaUrl: '',
		});
	};

	const onSelectMedia = (media) => {
		setAttributes({
			mediaId: media.id,
			mediaUrl: media.url,
		});
	};

	const htmlElementMessages = {
		header: __(
			'The <header> element should represent introductory content, typically a group of introductory or navigational aids.'
		),
		main: __('The <main> element should be used for the primary content of your document only.'),
		section: __(
			"The <section> element should represent a standalone portion of the document that can't be better represented by another element."
		),
		article: __('The <article> element should represent a self-contained, syndicatable portion of the document.'),
		aside: __(
			"The <aside> element should represent a portion of a document whose content is only indirectly related to the document's main content."
		),
		footer: __(
			'The <footer> element should represent a footer for its nearest sectioning element (e.g.: <section>, <article>, <main> etc.).'
		),
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Behaviour" initialOpen={false}>
					<ToggleControl
						label="Collapse vertical space"
						checked={attributes.collapseTopSpace === true && attributes.collapseBottomSpace === true}
						onChange={() => {
							setAttributes({
								collapseTopSpace: !attributes.collapseTopSpace,
								collapseBottomSpace: !attributes.collapseBottomSpace,
							});
						}}
					/>
					<ToggleControl
						label="Collapse top space"
						checked={attributes.collapseTopSpace === true}
						onChange={() => {
							setAttributes({
								collapseTopSpace: !attributes.collapseTopSpace,
							});
						}}
					/>
					<ToggleControl
						label="Collapse bottom space"
						help="If paired with another section, this will collapse the bottom space between the two sections. This is useful when you have 2 sections with the same background colour."
						checked={attributes.collapseBottomSpace === true}
						onChange={() => {
							setAttributes({
								collapseBottomSpace: !attributes.collapseBottomSpace,
							});
						}}
					/>
				</PanelBody>
				<PanelBody title="Background Image" initialOpen={false}>
					<div className="editor-post-featured-image">
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectMedia}
								multiple={false}
								render={({ open }) => (
									<Button
										className={
											attributes.mediaId == 0
												? 'editor-post-featured-image__toggle'
												: 'editor-post-featured-image__preview'
										}
										onClick={open}
									>
										{attributes.mediaId == 0 && 'Select an image'}
										{media != undefined && (
											<ResponsiveWrapper
												naturalWidth={media.media_details.width}
												naturalHeight={media.media_details.height}
											>
												<img src={media.source_url} />
											</ResponsiveWrapper>
										)}
									</Button>
								)}
							/>
						</MediaUploadCheck>
						{attributes.mediaId != 0 && (
							<>
								<Spacer />
								<Flex>
									<FlexItem>
										<MediaUploadCheck>
											<MediaUpload
												title="Replace Image"
												value={attributes.mediaId}
												onSelect={onSelectMedia}
												allowedTypes={['image']}
												render={({ open }) => (
													<Button onClick={open} variant="secondary" isLarge>
														Replace Image
													</Button>
												)}
											/>
										</MediaUploadCheck>
									</FlexItem>
									<FlexItem>
										<MediaUploadCheck>
											<Button onClick={removeMedia} variant="link" isDestructive>
												Remove Image
											</Button>
										</MediaUploadCheck>
									</FlexItem>
								</Flex>
							</>
						)}
					</div>
				</PanelBody>
			</InspectorControls>
			<InspectorControls group="advanced">
				<SelectControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={__('HTML element')}
					options={[
						{ label: __('Default (<div>)'), value: 'div' },
						{ label: '<header>', value: 'header' },
						{ label: '<main>', value: 'main' },
						{ label: '<section>', value: 'section' },
						{ label: '<article>', value: 'article' },
						{ label: '<aside>', value: 'aside' },
						{ label: '<footer>', value: 'footer' },
					]}
					value={attributes.tagName}
					onChange={(value) => setAttributes({ tagName: value })}
					help={htmlElementMessages[attributes.tagName]}
				/>
			</InspectorControls>
			<TagName {...blockProps}>
				<div {...innerBlockProps} />
			</TagName>
		</>
	);
}
