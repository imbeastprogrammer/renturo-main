import {
    AddIcon,
    DeleteIcon,
    EditUserIcon,
    NextIcon,
    ResetPasswordIcon,
    UserIcon,
} from '@/assets/central/dashboard';
import { Separator } from '@/components/ui/separator';
import { ReactNode } from 'react';

function UserCard() {
    return (
        <div className='h-full w-full rounded-lg bg-white p-4'>
            <div className='flex items-center justify-between'>
                <div className='flex items-center gap-4'>
                    <img src={UserIcon} alt='User Icon' />
                    <h1 className='text-lg text-black/80'>User</h1>
                </div>
                <img src={NextIcon} alt='Next Icon' />
            </div>
            <Separator className='my-2' />
            <div className='space-y-2 text-[15px]'>
                <Action icon={AddIcon}>Add a user</Action>
                <Action icon={DeleteIcon}>Delete a user</Action>
                <Action icon={EditUserIcon}>Edit a user</Action>
                <Action icon={ResetPasswordIcon}>Reset a password</Action>
            </div>
        </div>
    );
}

type ActionProps = { children: ReactNode; icon: string };

function Action({ children, icon }: ActionProps) {
    return (
        <div className='flex gap-4'>
            <img
                src={icon}
                alt='action icon'
                className='h-[20px] w-[20px] object-contain'
            />
            <span>{children}</span>
        </div>
    );
}

export default UserCard;
