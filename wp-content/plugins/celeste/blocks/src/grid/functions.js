import classnames from 'classnames';

export const generateClassName = (attributes) => {
	const verticalAlignmentClassNames = {
		top: 'has-start-vertical-align',
		center: 'has-center-vertical-align',
		bottom: 'has-end-vertical-align',
	};

	const gapClassNames = {
		sm: 'has-sm-gap',
		md: 'has-md-gap',
		lg: 'has-lg-gap',
		xl: 'has-xl-gap',
	};

	const defaultColumnClassNames = {
		1: 'has-1-columns',
		2: 'has-2-columns',
		3: 'has-3-columns',
		4: 'has-4-columns',
		5: 'has-5-columns',
		6: 'has-6-columns',
	};

	const mediumColumnClassNames = {
		1: 'md:has-1-columns',
		2: 'md:has-2-columns',
		3: 'md:has-3-columns',
		4: 'md:has-4-columns',
		5: 'md:has-5-columns',
		6: 'md:has-6-columns',
	};

	const largeColumnClassNames = {
		1: 'lg:has-1-columns',
		2: 'lg:has-2-columns',
		3: 'lg:has-3-columns',
		4: 'lg:has-4-columns',
		5: 'lg:has-5-columns',
		6: 'lg:has-6-columns',
	};

	return classnames(
		{ [defaultColumnClassNames[attributes.columns]]: attributes.columns },
		{ [mediumColumnClassNames[attributes.columnsMd]]: attributes.columnsMd },
		{ [largeColumnClassNames[attributes.columnsLg]]: attributes.columnsLg },
		{ [gapClassNames[attributes.gap]]: attributes.gap },
		{
			[verticalAlignmentClassNames[attributes.verticalAlignment]]: attributes.verticalAlignment,
		}
	);
};
