import RenturoTextLogoWhite from '@/assets/logo/RenturoLogoWhite.png';
import { OtpHero } from '@/assets/tenant/auth';

function ForgotPasswordHero() {
    return (
        <div className='relative grid items-center bg-metalic-blue p-8'>
            <img
                className='absolute left-8 top-8 h-[45px]'
                src={RenturoTextLogoWhite}
                alt='logo'
            />
            <div className='grid place-items-center gap-4 text-white'>
                <img src={OtpHero} alt='otp hero' />
                <h1 className='text-[52px] font-bold'>Welcome Back!</h1>
            </div>
        </div>
    );
}

export default ForgotPasswordHero;
