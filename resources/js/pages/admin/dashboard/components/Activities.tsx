import { FlagIcon } from 'lucide-react';
import profile from '@/assets/profile.png';

function Activities() {
    return (
        <article className='h-full rounded-lg border p-4 shadow-lg'>
            <h1 className='text-[22px] font-semibold'>Activities</h1>
            <ul className='mt-4 space-y-4'>
                <li>
                    <Activity />
                </li>
                <li>
                    <Activity />
                </li>
                <li>
                    <Activity />
                </li>
            </ul>
        </article>
    );
}

function Activity() {
    return (
        <div className='flex gap-4'>
            <div className='flex-shrink-0'>
                <img className='h-[50px] w-[50px]' src={profile} />
            </div>
            <div className='space-y-2'>
                <span className='flex items-center gap-4 text-sm font-semibold text-green-500'>
                    <FlagIcon className='h-4 w-4' /> New listing posted
                </span>
                <p className='text-sm leading-tight'>
                    <span className='font-semibold'>John Doe</span> posted a new
                    listing,
                    <span className='font-semibold'>
                        Father Blanco's Garden
                    </span>
                </p>
                <span className='text-sm text-gray-500'>Just Now</span>
            </div>
        </div>
    );
}

export default Activities;
