import { router } from '@inertiajs/react';
import { LogOutIcon } from 'lucide-react';

function LogoutButton() {
    const handleLogout = () => router.post('/logout');

    return (
        <LogOutIcon
            className='mx-auto h-[40px] w-[40px] cursor-pointer'
            onClick={handleLogout}
        />
    );
}

export default LogoutButton;
