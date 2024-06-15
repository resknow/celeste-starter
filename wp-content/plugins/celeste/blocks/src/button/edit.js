import apiFetch from '@wordpress/api-fetch';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import {
	BaseControl,
	Button,
	ButtonGroup,
	ColorPalette,
	Flex,
	FlexItem,
	Icon,
	PanelBody,
	SelectControl,
	DropdownMenu,
	TextControl,
	ToggleControl,
	MenuGroup,
	MenuItem,
} from '@wordpress/components';
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
export default function Edit({ attributes, setAttributes }) {
	/**
	 * The useBlockProps hook returns a set of props that'll be applied to the
	 * block's container element.
	 */
	const blockProps = useBlockProps();

	/**
	 * Setup the initial state of the block, we'll make an API call
	 * on render to get a list of icons
	 */
	const [icons, setIcons] = useState([]);
	const [iconSVG, setIconSVG] = useState('');

	/**
	 * Make an API call to get a list of icons and update the state
	 * so we can use those icons in the dropdown menu and in the
	 * block preview
	 */
	useEffect(() => {
		apiFetch({ path: '/celeste/v1/theme/icons' }).then((icons) => {
			setIcons([...icons]);
			const selectedIcon = icons.filter((icon) => icon.name === attributes.icon);
			return setIconSVG(selectedIcon.length ? selectedIcon[0].svg : '');
		});
	}, []);

	/**
	 * Create a list of variants and their corrosponding colors to
	 * use in the ColorPalette component
	 */
	const variantColors = [
		{
			name: 'Base',
			color: 'var(--wp--preset--color--base)',
			key: 'base',
		},
		{
			name: 'Contrast',
			color: 'var(--wp--preset--color--contrast)',
			key: 'contrast',
		},
		{
			name: 'Primary',
			color: 'var(--wp--preset--color--primary)',
			key: 'primary',
		},
		{
			name: 'Secondary',
			color: 'var(--wp--preset--color--secondary)',
			key: 'secondary',
		},
		{
			name: 'Tertiary',
			color: 'var(--wp--preset--color--tertiary)',
			key: 'tertiary',
		},
		{
			name: 'Highlight',
			color: 'var(--wp--preset--color--highlight)',
			key: 'highlight',
		},
	];

	/**
	 * Map over the icons and return a MenuItem for each one
	 * This will be used in the InspectorControls (sidebar)
	 */
	const MenuIcons = ({ onClose }) => {
		return (
			<>
				{icons.map((icon) => {
					return (
						<MenuItem
							onClick={() => {
								setAttributes({ icon: icon.name });
								setIconSVG(icon.svg);
								onClose();
							}}
						>
							<Flex justify="start" align="center">
								<FlexItem>
									<img
										src={icon.url}
										style={{
											width: '1.2em',
											height: '1.2em',
											display: 'block',
										}}
									/>
								</FlexItem>
								<FlexItem>{icon.label}</FlexItem>
							</Flex>
						</MenuItem>
					);
				})}
			</>
		);
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Appearance">
					<BaseControl label="Icon">
						<Flex justify="start" align="center">
							<FlexItem>
								<DropdownMenu
									label="Icon"
									icon={
										<Icon
											icon={
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
													<path d="M256 294.1L383 167c9.4-9.4 24.6-9.4 33.9 0s9.3 24.6 0 34L273 345c-9.1 9.1-23.7 9.3-33.1.7L95 201.1c-4.7-4.7-7-10.9-7-17s2.3-12.3 7-17c9.4-9.4 24.6-9.4 33.9 0l127.1 127z" />
												</svg>
											}
										/>
									}
								>
									{({ onClose }) => (
										<>
											<MenuGroup>
												<MenuIcons onClose={onClose} />
											</MenuGroup>
										</>
									)}
								</DropdownMenu>
							</FlexItem>
							{!attributes.icon && <FlexItem>No icon</FlexItem>}
							{attributes.icon && (
								<FlexItem>
									<code style={{ opacity: '.7' }}>{attributes.icon}</code>
								</FlexItem>
							)}
						</Flex>
						<div style={{ marginBlockStart: '1em' }}>
							<ToggleControl
								label="Icon after text"
								help={
									attributes.iconPosition === 'end'
										? 'Icon is placed after text'
										: 'Icon is placed before text'
								}
								checked={attributes.iconPosition === 'end'}
								onChange={() => {
									setAttributes({
										iconPosition: attributes.iconPosition === 'end' ? 'start' : 'end',
									});
								}}
							/>
						</div>
					</BaseControl>
					<BaseControl label="Variant">
						<ColorPalette
							colors={variantColors}
							value={`var(--wp--preset--color--${attributes.variant})`}
							onChange={(color) => {
								/**
								 * @todo Find a better way to get the variant name from the ColorPalette
								 * component, given that it will only return the color value.
								 */
								let selectedVariant = color.replace('var(--wp--preset--color--', '').replace(')', '');
								setAttributes({ variant: selectedVariant });
							}}
							clearable={false}
							disableCustomColors
						/>
					</BaseControl>
					<BaseControl label="Size">
						<div>
							<ButtonGroup>
								<Button
									variant={attributes.size === 'sm' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ size: 'sm' })}
								>
									Small
								</Button>
								<Button
									variant={attributes.size === 'md' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ size: 'md' })}
								>
									Medium
								</Button>
								<Button
									variant={attributes.size === 'lg' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ size: 'lg' })}
								>
									Large
								</Button>
							</ButtonGroup>
						</div>
					</BaseControl>
					<BaseControl label="Type">
						<div>
							<ButtonGroup>
								<Button
									variant={attributes.type === 'link' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ type: 'link' })}
								>
									Link
								</Button>
								<Button
									variant={attributes.type === 'button' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ type: 'button' })}
								>
									Button
								</Button>
								<Button
									variant={attributes.type === 'submit' ? 'primary' : 'secondary'}
									onClick={() => setAttributes({ type: 'submit' })}
								>
									Submit Button
								</Button>
							</ButtonGroup>
						</div>
					</BaseControl>
					{attributes.type === 'link' && (
						<TextControl
							label="Link to"
							value={attributes.href}
							onChange={(value) => setAttributes({ href: value })}
						/>
					)}
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<div
					className="btn"
					data-variant={attributes.variant}
					data-size={attributes.size}
					data-has-icon={attributes.icon}
					data-icon-position={attributes.iconPosition}
				>
					{attributes.icon && (
						<div
							className="btn__icon"
							dangerouslySetInnerHTML={{
								__html: iconSVG,
							}}
						/>
					)}
					<RichText
						tagName="span"
						value={attributes.text}
						onChange={(text) => setAttributes({ text })}
						placeholder={'Button text...'}
						style={{ cursor: 'text' }}
					/>
				</div>
			</div>
		</>
	);
}
