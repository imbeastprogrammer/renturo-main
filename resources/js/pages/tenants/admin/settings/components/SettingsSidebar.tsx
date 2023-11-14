import { ScrollArea } from '@/components/ui/scroll-area';
import UserPicture from './UserPicture';
import SettingsNavigation from './SettingsNavigation';

function SettingsSidebar() {
    return (
        <ScrollArea>
            <UserPicture />
            <SettingsNavigation />
        </ScrollArea>
    );
}

export default SettingsSidebar;
