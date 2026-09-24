/**
 * WordPress dependencies
 */
import { useInstanceId } from '@wordpress/compose';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Icon,
	chevronDown,
	chevronRight,
	external,
	help,
} from '@wordpress/icons';
import { Badge } from '@wordpress/ui';

/**
 * Internal dependencies
 */
import EnableMark from '../components/EnableMark';
import { SkeletonText } from '../components/skeleton';
import { DOCS_URL, VERSION } from '../data/boot';

/**
 * Full-height sidebar: plugin header, section links, docs link. On mobile the
 * list collapses behind a "current section" toggle.
 *
 * @param {Object} props
 * @param {Array}  props.tabs    Tab registry (empty while loading).
 * @param {string} props.current Active tab id.
 * @param {Object} props.meta    Optional node per tab id, after the label.
 */
export default function SideNav( { tabs, current, meta = {} } ) {
	const [ isOpen, setIsOpen ] = useState( false );
	const navId = useInstanceId( SideNav, 'lw-admin-sidenav' );
	const active = tabs.find( ( tab ) => tab.id === current );
	const home = tabs[ 0 ] ? `#${ tabs[ 0 ].id }` : '#';

	useEffect( () => setIsOpen( false ), [ current ] );

	return (
		<aside className={ `lw-admin-sidebar ${ isOpen ? 'is-open' : '' }` }>
			<div className="lw-admin-sidebar__head">
				<a
					className="lw-admin-sidebar__home"
					href={ home }
					aria-label={ __( 'LW Enable home', 'lw-enable' ) }
				>
					<EnableMark />
					<strong>LW Enable</strong>
				</a>
				<Badge intent="informational">{ `v${ VERSION }` }</Badge>
			</div>
			{ active && (
				<button
					type="button"
					className="lw-admin-sidebar__toggle"
					aria-expanded={ isOpen }
					aria-controls={ navId }
					onClick={ () => setIsOpen( ! isOpen ) }
				>
					<Icon icon={ active.icon } size={ 20 } />
					<span>{ active.label }</span>
					<Icon icon={ chevronDown } size={ 20 } />
				</button>
			) }
			<nav
				id={ navId }
				className="lw-admin-sidenav"
				aria-label={ __( 'LW Enable sections', 'lw-enable' ) }
			>
				{ tabs.length === 0 && (
					<span
						className="lw-skel-stack lw-skel-nav"
						aria-hidden="true"
					>
						<SkeletonText width="70%" />
					</span>
				) }
				<ul>
					{ tabs.map( ( tab ) => {
						const isCurrent = tab.id === current;
						return (
							<li key={ tab.id }>
								<a
									href={ `#${ tab.id }` }
									className="lw-admin-sidenav__item"
									aria-current={
										isCurrent ? 'page' : undefined
									}
								>
									<Icon icon={ tab.icon } size={ 20 } />
									<span className="lw-admin-sidenav__label">
										{ tab.label }
									</span>
									{ meta[ tab.id ] && (
										<span className="lw-admin-sidenav__meta">
											{ meta[ tab.id ] }
										</span>
									) }
									{ isCurrent && (
										<Icon
											icon={ chevronRight }
											size={ 18 }
										/>
									) }
								</a>
							</li>
						);
					} ) }
				</ul>
			</nav>
			<div className="lw-admin-sidebar__foot">
				<a
					className="lw-admin-sidenav__item"
					href={ DOCS_URL }
					target="_blank"
					rel="noopener noreferrer"
				>
					<Icon icon={ help } size={ 20 } />
					<span className="lw-admin-sidenav__label">
						{ __( 'Documentation', 'lw-enable' ) }
					</span>
					<Icon icon={ external } size={ 16 } />
					<span className="screen-reader-text">
						{ __( '(opens in a new tab)', 'lw-enable' ) }
					</span>
				</a>
			</div>
		</aside>
	);
}
