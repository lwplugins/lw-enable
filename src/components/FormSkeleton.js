/**
 * Internal dependencies
 */
import { SkeletonRegion, SkeletonRows, SkeletonSection } from './skeleton';

/**
 * Placeholder for a settings section: one card of switch rows.
 */
export default function FormSkeleton() {
	return (
		<SkeletonRegion className="lw-skel-tab">
			<SkeletonSection description={ false }>
				<SkeletonRows count={ 1 } />
			</SkeletonSection>
		</SkeletonRegion>
	);
}
