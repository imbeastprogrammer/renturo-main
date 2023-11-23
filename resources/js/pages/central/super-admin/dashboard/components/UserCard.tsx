import { ReactNode } from 'react';
import { IconType } from 'react-icons';
import { FaArrowRight } from 'react-icons/fa';
import { FaCirclePlus } from 'react-icons/fa6';
import { HiTrash } from 'react-icons/hi2';
import { FaUser } from 'react-icons/fa6';
import { BiSolidEditAlt } from 'react-icons/bi';
import { MdOutlineLockReset } from 'react-icons/md';
import { Separator } from '@/components/ui/separator';

function UserCard() {
    return (
        <div className='h-full w-full rounded-lg bg-white p-4'>
            <div className='flex items-center justify-between'>
                <div className='flex items-center gap-4'>
                    <FaUser className='h-[25px] w-[25px] text-black/80' />
                    <h1 className='text-lg text-black/80'>User</h1>
                </div>
                <FaArrowRight className='h-[25px] w-[25px] text-black/80' />
            </div>
            <Separator className='my-2' />
            <div className='space-y-2 text-[15px]'>
                <Action icon={FaCirclePlus}>Add a user</Action>
                <Action icon={HiTrash}>Delete a user</Action>
                <Action icon={BiSolidEditAlt}>Edit a user</Action>
                <Action icon={MdOutlineLockReset}>Reset a password</Action>
            </div>
        </div>
    );
}

type ActionProps = { children: ReactNode; icon: IconType };

function Action({ children, icon: Icon }: ActionProps) {
    return (
        <div className='flex gap-4'>
            <Icon className='h-[20px] w-[20px] flex-shrink-0 text-black/60' />
            <span>{children}</span>
        </div>
    );
}

export default UserCard;
