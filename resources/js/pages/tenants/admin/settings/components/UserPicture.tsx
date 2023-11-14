import profilePicture from '@/assets/profile.png';
import { Badge } from '@/components/ui/badge';
import { CheckCircleIcon } from 'lucide-react';

function UserPicture() {
    return (
        <div className='bg-off-white flex h-max w-full flex-col items-center space-y-4 rounded-lg bg-[#F3F7FD] p-4'>
            <img className='h-[100px] w-[100px]' src={profilePicture} />
            <div className='flex items-center gap-4 text-[18px] font-semibold'>
                <h1>Jane Cooper</h1>
                <CheckCircleIcon className='text-metalic-blue' />
            </div>
            <Badge className='pointer-events-none bg-metalic-blue p-2 px-8 uppercase'>
                Admin
            </Badge>
        </div>
    );
}

export default UserPicture;
