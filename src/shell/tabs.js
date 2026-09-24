/**
 * WordPress dependencies
 */
import { cog, media } from '@wordpress/icons';

/**
 * Section dashicon (FieldsData) mapped to a WordPress icons-package icon.
 */
const ICONS = {
	'dashicons-admin-media': media,
};

/**
 * Tab registry built from the server's `meta.sections` (FieldsData order).
 * Every section edits lw_enable options, so the top bar Save is always shown.
 *
 * @param {Array} sections Settings meta sections.
 * @return {Array} Tabs: { id, label, title, icon, fields }.
 */
export const toTabs = ( sections = [] ) =>
	sections.map( ( section ) => ( {
		id: section.key,
		label: section.title,
		title: section.title,
		icon: ICONS[ section.icon ] || cog,
		fields: section.fields,
	} ) );
