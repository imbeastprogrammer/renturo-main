import { usePage } from '@inertiajs/react';
import { Separator } from '@/components/ui/separator';
import { HiBell } from 'react-icons/hi';
import { User } from '@/types/users';

const UserRoleMap: Record<string, string> = {
    'SUPER-ADMIN': 'Super Admin',
};

function UserButton() {
    const props = usePage().props;
    const auth = props.auth as { user: User };
    const user = auth.user;

    return (
        <div className='flex h-full items-center gap-x-6'>
            <button type='button' className='relative'>
                <HiBell className='h-[30px] w-[30px] text-yinmn-blue' />
                <div className='absolute right-0 top-0 h-[6px] w-[6px] rounded-full bg-red-500'></div>
            </button>
            <Separator
                orientation='vertical'
                className='h-[33px] w-[2px] bg-[#545557]/30'
            />
            <div>
                <h1 className='text-[22px] font-medium leading-none'>
                    {[user.first_name, user.last_name].join(' ')}
                </h1>
                <span className='text-base font-light'>
                    {UserRoleMap[user.role] || user.role}
                </span>
            </div>
            <div className='h-[50px] w-[50px] rounded-full bg-yinmn-blue'></div>
        </div>
    );
}

export default UserButton;
