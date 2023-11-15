import KabootekTextLogoWhite from '@/assets/central/auth/kabootek-text-logo-white.png';

function LoginHero() {
    return (
        <div className='relative grid place-items-center bg-yinmn-blue p-8'>
            <img
                className='absolute left-6 top-6 h-[36px]'
                src={KabootekTextLogoWhite}
                alt='logo'
            />
            <div className='text-white'>
                <h1 className='text-[52px] font-bold'>Login to your account</h1>
                <p className='text-[22px]'>
                    Enter your email address and password below to log in to
                    your account.
                </p>
            </div>
        </div>
    );
}

export default LoginHero;
