import { FiSettings } from 'react-icons/fi';

function UserButton() {
    return (
        <div className='flex items-center gap-6 text-white'>
            <div className='h-[50px] w-[50px] rounded-full bg-white'></div>
            <div>
                <h1 className='text-[22px] font-medium leading-none'>
                    John Doe
                </h1>
                <span className='text-base'>Ads Partner</span>
            </div>
            <FiSettings className='h-[30px] w-[30px]' />
        </div>
    );
}

export default UserButton;
