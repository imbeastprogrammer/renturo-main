import { ReactNode } from 'react';
import SettingsLayout from '../components/SettingsLayout';

function PersonalInformation() {
    return <div>PersonalInformation</div>;
}

PersonalInformation.layout = (page: ReactNode) => (
    <SettingsLayout>{page}</SettingsLayout>
);

export default PersonalInformation;
