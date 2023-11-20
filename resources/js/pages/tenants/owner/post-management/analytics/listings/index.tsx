import { ReactNode } from 'react';
import OwnerLayout from '@/layouts/OwnerLayout';

function Listings() {
    return <div>Listings</div>;
}

Listings.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default Listings;
