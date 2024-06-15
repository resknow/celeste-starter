import classnames from 'classnames';

export const generateClassName = (attributes) => {
	const colSpanClasses = {
		1: 'spans-1-columns',
		2: 'spans-2-columns',
		3: 'spans-3-columns',
		4: 'spans-4-columns',
		5: 'spans-5-columns',
		6: 'spans-6-columns',
	};

	const colStartClasses = {
		null: '',
		1: 'starts-at-1-column',
		2: 'starts-at-2-columns',
		3: 'starts-at-3-columns',
		4: 'starts-at-4-columns',
		5: 'starts-at-5-columns',
		6: 'starts-at-6-columns',
	};

	return classnames({
		[colSpanClasses[attributes.span]]: attributes.span,
		[colStartClasses[attributes.start]]: attributes.start,
	});
};
