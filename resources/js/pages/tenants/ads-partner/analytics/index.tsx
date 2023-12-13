import { ReactNode } from 'react';
import AdsPartnerLayout from '@/layouts/AdsPartnerLayout';
import Overview from './components/Overview';
import Revenue from './components/Revenue';
import RevenueByAd from './components/RevenueByAd';
import AudienceOverview from './components/AudienceOverview';
import Sales from './components/Sales';

function Analytics() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-4'>
            <h1 className='text-[48px] font-semibold'>Analytics</h1>
            <div
                className='grid grid-cols-5 grid-rows-2 gap-4'
                style={{
                    gridTemplateAreas: `
                    "overview           revenue            revenue       revenue-by-ad  revenue-by-ad"
                    "audience-overview  audience-overview  sales         sales          sales"
            `,
                }}
            >
                <div style={{ gridArea: 'overview' }}>
                    <Overview />
                </div>
                <div style={{ gridArea: 'revenue' }}>
                    <Revenue />
                </div>
                <div style={{ gridArea: 'revenue-by-ad' }}>
                    <RevenueByAd />
                </div>
                <div style={{ gridArea: 'audience-overview' }}>
                    <AudienceOverview />
                </div>
                <div style={{ gridArea: 'sales' }}>
                    <Sales />
                </div>
            </div>
        </div>
    );
}

Analytics.layout = (page: ReactNode) => (
    <AdsPartnerLayout>{page}</AdsPartnerLayout>
);

export default Analytics;
