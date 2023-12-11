import AdsPartnerLayout from '@/layouts/AdsPartnerLayout';
import { ReactNode } from 'react';

function Dashboard() {
    return <div>Dashboard</div>;
}

Dashboard.layout = (page: ReactNode) => (
    <AdsPartnerLayout>{page}</AdsPartnerLayout>
);

export default Dashboard;
