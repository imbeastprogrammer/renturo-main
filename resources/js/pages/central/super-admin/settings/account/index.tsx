import { ReactNode } from 'react';
import SettingsLayout from '../components/SettingsLayout';
import UpdateAccountForm from './components/UpdateAccountForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function Account() {
    return (
        <ScrollArea>
            <UpdateAccountForm />
        </ScrollArea>
    );
}

Account.layout = (page: ReactNode) => <SettingsLayout>{page}</SettingsLayout>;

export default Account;
