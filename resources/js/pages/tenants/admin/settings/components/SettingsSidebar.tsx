import { ScrollArea } from '@/components/ui/scroll-area';
import UserPicture from './UserPicture';
import SettingsNavigation from './SettingsNavigation';

function SettingsSidebar() {
    return (
        <ScrollArea className='p-4'>
            <UserPicture />
            <SettingsNavigation />
        </ScrollArea>
    );
}

export default SettingsSidebar;
