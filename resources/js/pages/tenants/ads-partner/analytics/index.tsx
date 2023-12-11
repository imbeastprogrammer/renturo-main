import { ReactNode } from 'react';
import AdsPartnerLayout from '@/layouts/AdsPartnerLayout';

function Analytics() {
    return <div>Analytics</div>;
}

Analytics.layout = (page: ReactNode) => (
    <AdsPartnerLayout>{page}</AdsPartnerLayout>
);

export default Analytics;
