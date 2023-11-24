import { LoginHero } from '@/assets/tenant/auth';
import RenturoTextLogoBlue from '@/assets/logo/RenturoLogoWhite.png';
import LoginForm from './components/LoginForm';
import { Link } from '@inertiajs/react';

function RegisterPage() {
    return (
        <div className='grid h-screen grid-cols-[610px_1fr] gap-8'>
            <div className='grid w-full place-items-center bg-metalic-blue p-8'>
                <img
                    src={RenturoTextLogoBlue}
                    alt='app logo'
                    className='absolute left-8 top-8 h-[45px] object-contain'
                />
                <div className='grid place-items-center gap-4'>
                    <img src={LoginHero} alt='login hero' />
                    <h1 className='text-center text-[52px] font-bold leading-none text-white'>
                        Register a new account
                    </h1>
                </div>
            </div>
            <div className='grid grid-rows-[auto_1fr]'>
                <div className='flex items-center justify-end gap-4 p-8 text-lg text-black/40'>
                    Already have an account? Login your account here
                    <Link
                        href='/login'
                        className='grid h-[37px] w-[77px] place-items-center rounded-sm border border-metalic-blue text-sm font-semibold uppercase text-metalic-blue'
                    >
                        Login
                    </Link>
                </div>
                <LoginForm />
            </div>
        </div>
    );
}

export default RegisterPage;
