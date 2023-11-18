import { ReactNode } from 'react';
import OwnerLayout from '@/layouts/OwnerLayout';

function Dashboard() {
    return <div>Dashboard</div>;
}

Dashboard.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default Dashboard;
