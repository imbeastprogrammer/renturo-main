import AdminLayout from '@/layouts/AdminLayout';
import MonthlyRevenue from './components/MonthlyRevenue';

function Dashboard() {
    return (
        <div
            style={{
                display: 'grid',
                gridTemplateColumns: 'repeat(4,1fr)',
                gridTemplateRows: 'repeat(2,1fr)',
                gridTemplateAreas: `"monthly-revenue monthly-revenue monthly-trends overview"
                                    "activities      recent-bookings recent-bookings overview`,
                gap: '1rem',
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
            <div style={{ gridArea: 'overview' }} className='bg-red-500'>
                <MonthlyRevenue />
            </div>
            <div style={{ gridArea: 'activities' }} className='bg-gray-500'>
                <MonthlyRevenue />
            </div>
            <div
                style={{ gridArea: 'recent-bookings' }}
                className='bg-blue-300'
            >
                <MonthlyRevenue />
            </div>
        </div>
    );
}

Dashboard.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default Dashboard;
