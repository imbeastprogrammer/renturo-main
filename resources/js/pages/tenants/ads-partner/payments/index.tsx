import { ReactNode } from 'react';
import AdsPartnerLayout from '@/layouts/AdsPartnerLayout';

function Payments() {
    return <div>Payments</div>;
}

Payments.layout = (page: ReactNode) => (
    <AdsPartnerLayout>{page}</AdsPartnerLayout>
);

export default Payments;
