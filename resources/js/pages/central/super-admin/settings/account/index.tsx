import { ReactNode } from 'react';
import SettingsLayout from '../components/SettingsLayout';

function Account() {
    return <div>User Profile</div>;
}

Account.layout = (page: ReactNode) => <SettingsLayout>{page}</SettingsLayout>;

export default Account;
