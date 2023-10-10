import AdminLayout from '@/layouts/AdminLayout';

function Dashboard() {
    return <div>Dashboard</div>;
}

Dashboard.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default Dashboard;
