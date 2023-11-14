import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';
import SettingsLayout from '../components/SettingsLayout';
import ChangePasswordForm from './components/ChangePasswordForm';

function ChangePassword() {
    return (
        <ScrollArea>
            <ChangePasswordForm />
        </ScrollArea>
    );
}
ChangePassword.layout = (page: ReactNode) => (
    <SettingsLayout>{page}</SettingsLayout>
);

export default ChangePassword;
