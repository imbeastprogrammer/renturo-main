import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';

function Ads() {
    return <div>Ads</div>;
}

Ads.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default Ads;
