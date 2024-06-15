import classnames from "classnames";

export const generateClassName = (attributes) => {
	return classnames(
		"section",
		{ "has-background-image": attributes.mediaId },
		{ "has-collapsed-bottom-space": attributes.collapseBottomSpace },
		{ "has-collapsed-top-space": attributes.collapseTopSpace }
	);
};
