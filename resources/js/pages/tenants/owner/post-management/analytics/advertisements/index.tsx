import { ReactNode } from 'react';
import OwnerLayout from '@/layouts/OwnerLayout';

function Advertisements() {
    return <div>Ads</div>;
}

Advertisements.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default Advertisements;
