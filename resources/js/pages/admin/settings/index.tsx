import AdminLayout from '@/layouts/AdminLayout';
import UserPicture from './components/UserPicture';
import SettingsNavigation from './components/SettingsNavigation';
import PersonalInformation from './components/PersonalInformation';

function SettingsPage() {
    return (
        <AdminLayout>
            <div className='grid h-full grid-cols-[300px_auto] overflow-hidden rounded-lg border shadow-lg'>
                <div className='space-y-4 overflow-auto p-6'>
                    <UserPicture />
                    <SettingsNavigation />
                </div>
                <div className='overflow-auto p-6'>
                    <PersonalInformation />
                </div>
            </div>
        </AdminLayout>
    );
}

export default SettingsPage;
