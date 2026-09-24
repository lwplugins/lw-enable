/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Section from '../components/Section';
import ToggleRow from '../components/ToggleRow';

/**
 * One settings section (FieldsData): a card of on/off switches. A switch ON
 * means the feature is enabled.
 *
 * @param {Object} props
 * @param {Object} props.tab   Tab ({ title, fields }).
 * @param {Object} props.store Settings store.
 */
export default function SectionTab( { tab, store } ) {
	return (
		<Section title={ tab.title }>
			{ tab.fields.map( ( field ) => (
				<ToggleRow
					key={ field.key }
					title={ field.label }
					help={ field.description }
					checked={ !! store.data.options[ field.key ] }
					onChange={ ( value ) => store.set( field.key, value ) }
					onText={ __( 'Enabled', 'lw-enable' ) }
					offText={ __( 'Disabled', 'lw-enable' ) }
				/>
			) ) }
		</Section>
	);
}
