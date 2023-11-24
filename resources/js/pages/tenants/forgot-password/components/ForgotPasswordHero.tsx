import RenturoTextLogoWhite from '@/assets/logo/RenturoLogoWhite.png';
import { CreateNewPassword } from '@/assets/tenant/auth';

function ForgotPasswordHero() {
    return (
        <div className='relative grid items-center bg-metalic-blue p-8'>
            <img
                className='absolute left-8 top-8 h-[45px]'
                src={RenturoTextLogoWhite}
                alt='app logo'
            />
            <div className='grid place-items-center gap-4'>
                <img
                    src={CreateNewPassword}
                    alt='create new password hero logo'
                />
                <h1 className='text-center text-[45px] font-bold text-white'>
                    Keep your account safe!
                </h1>
            </div>
        </div>
    );
}

export default ForgotPasswordHero;
