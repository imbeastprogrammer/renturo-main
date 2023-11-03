import { usePage } from '@inertiajs/react';
import { User } from '@/types/users';
import { BellIcon, ChevronDownIcon } from 'lucide-react';
import { Separator } from './ui/separator';
import proflle from '@/assets/profile.png';

function UserButton() {
    const props = usePage().props;
    const auth = props.auth as { user: User };
    const user = auth.user;

    return (
        <div className='flex h-[55px] items-center gap-4 rounded-xl border bg-white p-2 px-3'>
            <div className='relative text-metalic-blue'>
                <BellIcon />
                <div className='absolute -right-1 -top-1 h-2 w-2 rounded-full bg-red-500' />
            </div>
            <Separator orientation='vertical' />
            <h1 className='text-lg font-semibold'>
                {[user.first_name, user.last_name].join(' ')}
            </h1>
            <div className='h-[40px] w-[40px] overflow-hidden rounded-full bg-metalic-blue p-[2px]'>
                <img src={proflle} className='h-full w-full' />
            </div>
            <ChevronDownIcon />
        </div>
    );
}

export default UserButton;
