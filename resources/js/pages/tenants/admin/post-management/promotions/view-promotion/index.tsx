import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';

function ViewPromotion() {
    return <div>index</div>;
}

ViewPromotion.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default ViewPromotion;
