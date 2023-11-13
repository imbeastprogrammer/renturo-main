import { Separator } from '@/components/ui/separator';
import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import ChangePasswordForm from './components/ChangePasswordForm';
import Sidebar from '../components/Sidebar';
import { ScrollArea } from '@/components/ui/scroll-area';

function ChangePassword() {
    return (
        <div className='grid h-full grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <div className='grid grid-cols-[263px_auto_1fr] overflow-hidden rounded-xl border bg-white p-4 shadow-lg'>
                <Sidebar />
                <Separator orientation='vertical' />
                <ScrollArea>
                    <ChangePasswordForm />
                </ScrollArea>
            </div>
        </div>
    );
}

ChangePassword.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default ChangePassword;
