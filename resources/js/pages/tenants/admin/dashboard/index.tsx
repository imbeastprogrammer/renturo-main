import AdminLayout from '@/layouts/AdminLayout';
import MonthlyRevenue from './components/MonthlyRevenue';
import Activities from './components/Activities';
import RecentBookings from './components/RecentBookings';
import Overview from './components/Overview';
import MontlyTrends from './components/MontlyTrends';

function Dashboard() {
    return (
        <div
            style={{
                display: 'grid',
                gridTemplateColumns: 'repeat(4,1fr)',
                gridTemplateRows: '300px 1fr',
                gridTemplateAreas: `"monthly-revenue monthly-revenue monthly-trends overview"
                                    "activities      recent-bookings recent-bookings overview`,
                gap: '1rem',
                height: '100%',
                overflow: 'auto',
            }}
        >
            <div style={{ gridArea: 'monthly-revenue' }}>
                <MonthlyRevenue />
            </div>
            <div style={{ gridArea: 'monthly-trends' }}>
                <MontlyTrends />
            </div>
            <div style={{ gridArea: 'overview' }}>
                <Overview />
            </div>
            <div style={{ gridArea: 'activities', overflow: 'hidden' }}>
                <Activities />
            </div>
            <div style={{ gridArea: 'recent-bookings', overflow: 'hidden' }}>
                <RecentBookings />
            </div>
        </div>
    );
}

Dashboard.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default Dashboard;
