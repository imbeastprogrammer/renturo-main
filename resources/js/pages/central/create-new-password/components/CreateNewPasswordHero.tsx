import KabootekTextLogoWhite from '@/assets/central/auth/kabootek-text-logo-white.png';

function ChangePasswordHero() {
    return (
        <div className='relative grid items-center bg-yinmn-blue p-8'>
            <img
                className='absolute left-8 top-8 h-[36px]'
                src={KabootekTextLogoWhite}
                alt='logo'
            />
            <div className='text-white'>
                <h1 className='text-[52px] font-bold'>Change Password</h1>
            </div>
        </div>
    );
}

export default ChangePasswordHero;
