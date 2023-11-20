import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';

import { User } from '@/types/users';
import SettingsLayout from '../components/SettingsLayout';
import UpdateAccountForm from './components/UpdateAccountForm';

type AccountProps = { user: User };
function Account({ user }: AccountProps) {
    return (
        <ScrollArea>
            <UpdateAccountForm user={user} />
        </ScrollArea>
    );
}

Account.layout = (page: ReactNode) => <SettingsLayout>{page}</SettingsLayout>;

export default Account;
