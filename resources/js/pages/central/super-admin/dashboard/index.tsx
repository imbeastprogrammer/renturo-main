import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import Searchbar from './components/Searchbar';
import UserCard from './components/UserCard';
import Notifications from './components/Notifications';
import { ScrollArea } from '@/components/ui/scroll-area';

function Dashboard() {
    return (
        <ScrollArea className='h-full overflow-hidden p-4'>
            <div className='grid h-full grid-rows-[auto_1fr] gap-y-6'>
                <div>
                    <h1 className='mb-4 text-[36px] font-semibold'>
                        Dashboard
                    </h1>
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
        </ScrollArea>
    );
}

Dashboard.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default Dashboard;
