import AdminLayout from '@/layouts/AdminLayout';
import MonthlyRevenue from './components/MonthlyRevenue';
import Activities from './components/Activities';
import RecentBookings from './components/RecentBookings';
import Overview from './components/Overview';

function Dashboard() {
    return (
        <div
            style={{
                display: 'grid',
                gridTemplateColumns: 'repeat(4,1fr)',
                gridTemplateRows: '250px 1fr',
                gridTemplateAreas: `"monthly-revenue monthly-revenue monthly-trends overview"
                                    "activities      recent-bookings recent-bookings overview`,
                gap: '1rem',
                overflow: 'hidden',
            }}
        >
            <div
                style={{ gridArea: 'monthly-revenue' }}
                className='bg-blue-500'
            >
                <MonthlyRevenue />
            </div>
            <div
                style={{ gridArea: 'monthly-trends' }}
                className='bg-green-500'
            >
                <MonthlyRevenue />
            </div>
            <div style={{ gridArea: 'overview' }}>
                <Overview />
            </div>
            <div style={{ gridArea: 'activities' }}>
                <Activities />
            </div>
            <div
                style={{ gridArea: 'recent-bookings' }}
                className='overflow-hidden'
            >
                <RecentBookings />
            </div>
        </div>
    );
}

Dashboard.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default Dashboard;
