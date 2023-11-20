import { ReactNode } from 'react';
import OwnerLayout from '@/layouts/OwnerLayout';

function Promotions() {
    return <div>Promotions</div>;
}

Promotions.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default Promotions;
