import { LoginOtpHeroLogo } from '@/assets/central/auth';

function LoginOtpHero() {
    return (
        <div className='bg-yinmn-blue grid place-items-center rounded-lg p-4'>
            <div className='space-y-4'>
                <h1 className='text-center text-[35px] font-bold text-white'>
                    Welcome Back!
                </h1>
                <LoginOtpHeroLogo />
            </div>
        </div>
    );
}

export default LoginOtpHero;
