import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';

function Advertisements() {
    return <div>Ads</div>;
}

Advertisements.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default Advertisements;
