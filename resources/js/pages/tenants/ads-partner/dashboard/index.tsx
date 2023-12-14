import AdsPartnerLayout from '@/layouts/AdsPartnerLayout';
import { ReactNode } from 'react';
import AdActivity from './components/AdActivity';
import EarningsReport from './components/EarningsReport';
import Notifications from './components/Notifications';
import AdsActivity from './components/AdsActivity';
import PaymentRecords from './components/PaymentRecords';

function Dashboard() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-4'>
            <h1 className='text-[48px] font-semibold'>Dashboard</h1>
            <div
                className='grid grid-cols-8 grid-rows-2 gap-4'
                style={{
                    gridTemplateAreas: ` 
                    'ad-activity  ad-activity   earnings-report earnings-report  earnings-report notifications notifications notifications' 
                    'ads-activity ads-activity  ads-activity    ads-activity   payment-records payment-records payment-records payment-records'  
                    `,
                }}
            >
                <div style={{ gridArea: 'ad-activity' }}>
                    <AdActivity />
                </div>
                <div style={{ gridArea: 'earnings-report' }}>
                    <EarningsReport />
                </div>
                <div style={{ gridArea: 'notifications' }}>
                    <Notifications />
                </div>
                <div style={{ gridArea: 'ads-activity' }}>
                    <AdsActivity />
                </div>
                <div style={{ gridArea: 'payment-records' }}>
                    <PaymentRecords />
                </div>
            </div>
        </div>
    );
}

Dashboard.layout = (page: ReactNode) => (
    <AdsPartnerLayout>{page}</AdsPartnerLayout>
);

export default Dashboard;
