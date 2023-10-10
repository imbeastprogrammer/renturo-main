import AdminLayout from '@/layouts/AdminLayout';
import UserPicture from './components/UserPicture';
import SettingsNavigation from './components/SettingsNavigation';
import PersonalInformation from './components/PersonalInformation';

function View() {
    return (
        <div className='grid h-full grid-cols-[300px_auto] overflow-hidden rounded-lg border shadow-lg'>
            <div className='space-y-4 overflow-auto p-6'>
                <UserPicture />
                <SettingsNavigation />
            </div>
            <div className='overflow-auto p-6'>
                <PersonalInformation />
            </div>
        </div>
    );
}

View.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default View;
