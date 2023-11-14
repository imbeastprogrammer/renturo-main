import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';
import SettingsLayout from '../components/SettingsLayout';
import UpdateUserForm from './components/UpdateUserForm';

function PersonalInformation() {
    return (
        <div className='h-full overflow-hidden'>
            <ScrollArea className='h-full'>
                <UpdateUserForm />
            </ScrollArea>
        </div>
    );
}

PersonalInformation.layout = (page: ReactNode) => (
    <SettingsLayout>{page}</SettingsLayout>
);

export default PersonalInformation;
