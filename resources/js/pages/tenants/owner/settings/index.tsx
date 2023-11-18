import { ReactNode } from 'react';
import OwnerLayout from '@/layouts/OwnerLayout';

function Settings() {
    return <div>Settings</div>;
}

Settings.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default Settings;
