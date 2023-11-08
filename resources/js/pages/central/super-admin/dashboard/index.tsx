import SuperAdminLayout from '@/layouts/SuperAdminLayout';

function Dashboard() {
    return <div>Dashboard</div>;
}

Dashboard.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default Dashboard;
