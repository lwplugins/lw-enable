/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import FormSkeleton from './components/FormSkeleton';
import LoadError from './components/LoadError';
import Notices from './components/Notices';
import useSettingsStore from './data/useSettingsStore';
import Footer from './shell/Footer';
import SideNav from './shell/SideNav';
import TopBar from './shell/TopBar';
import { toTabs } from './shell/tabs';
import useSaveShortcut from './shell/useSaveShortcut';
import useTab from './shell/useTab';
import useUnsavedWarning from './shell/useUnsavedWarning';
import SectionTab from './tabs/SectionTab';

// Links like ?page=lw-enable&tab=media open the matching section.
const INITIAL_TAB =
	new URLSearchParams( window.location.search ).get( 'tab' ) || '';

/**
 * Shell + one options store shared by every section (partial saves). The
 * sections come from the server (FieldsData), so new switches need no JS.
 */
export default function App() {
	const store = useSettingsStore();
	const tabs = toTabs( store.data?.meta?.sections );
	const tab = useTab(
		tabs.map( ( t ) => t.id ),
		INITIAL_TAB
	);
	const current = tabs.find( ( t ) => t.id === tab );

	useUnsavedWarning( store.hasEdits );
	useSaveShortcut( store.save, store.hasEdits && ! store.isSaving );

	let content = null;
	if ( store.error ) {
		content = (
			<LoadError message={ store.error } onRetry={ store.reload } />
		);
	} else if ( store.isLoading ) {
		content = <FormSkeleton />;
	} else if ( current ) {
		content = <SectionTab tab={ current } store={ store } />;
	}

	return (
		<>
			<div className="lw-admin-shell">
				<SideNav tabs={ tabs } current={ tab } />
				<div className="lw-admin-main">
					<TopBar
						title={
							current
								? current.title
								: __( 'Settings', 'lw-enable' )
						}
						store={ store.data ? store : null }
					/>
					<main className="lw-admin-scroll">
						<div className="lw-admin-content">{ content }</div>
					</main>
					<Footer />
				</div>
			</div>
			<Notices />
		</>
	);
}
