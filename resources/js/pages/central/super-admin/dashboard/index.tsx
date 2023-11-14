import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import Searchbar from './components/Searchbar';
import UserCard from './components/UserCard';
import Notifications from './components/Notifications';

function Dashboard() {
    return (
        <div className='space-y-6 p-4'>
            <div>
                <h1 className='mb-4 text-[36px] font-semibold'>Dashboard</h1>
                <Searchbar />
            </div>
            <div className='grid grid-cols-4 grid-rows-3 gap-4'>
                <UserCard />
                <UserCard />
                <div className='col-span-2 row-span-2 h-full w-full'>
                    <Notifications />
                </div>
                <UserCard />
                <UserCard />
                <UserCard />
                <UserCard />
                <UserCard />
                <UserCard />
            </div>
        </div>
    );
}

Dashboard.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default Dashboard;
