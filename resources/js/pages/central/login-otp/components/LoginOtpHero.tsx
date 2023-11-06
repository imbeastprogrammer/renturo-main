import { LoginOtpHeroLogo } from '@/assets/central/login-otp';

function LoginOtpHero() {
    return (
        <div className='grid place-items-center rounded-lg bg-gradient-to-br from-[#185ADC] to-[#1B49A5] p-4'>
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
