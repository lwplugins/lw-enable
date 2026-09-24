/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';

const readHash = () => window.location.hash.replace( '#', '' );

/**
 * Current tab from location.hash; the first load also honours the classic
 * `?tab=` URL. The tab ids come from the server (settings meta), so the
 * requested tab is kept until they arrive and resolved against them.
 *
 * @param {string[]} ids     Tab ids (empty while loading).
 * @param {string}   initial Tab from the URL (?tab=).
 * @return {string} Current tab id ('' while there are no tabs).
 */
export default function useTab( ids, initial ) {
	const [ requested, setRequested ] = useState( () => readHash() || initial );

	useEffect( () => {
		const onChange = () => {
			setRequested( readHash() );
			window.scrollTo( { top: 0 } );
		};
		window.addEventListener( 'hashchange', onChange );
		return () => window.removeEventListener( 'hashchange', onChange );
	}, [] );

	return ids.includes( requested ) ? requested : ids[ 0 ] || '';
}
