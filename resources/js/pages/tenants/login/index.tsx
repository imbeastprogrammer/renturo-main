import { LoginHero } from '@/assets/tenant/auth';
import RenturoTextLogoBlue from '@/assets/logo/RenturoLogoWhite.png';
import LoginForm from './components/LoginForm';

function LoginPage() {
    return (
        <div className='grid h-screen grid-cols-[610px_1fr] place-items-center gap-8'>
            <div className='relative grid h-full w-full place-items-center bg-metalic-blue p-8'>
                <img
                    src={RenturoTextLogoBlue}
                    alt='app logo'
                    className='absolute left-8 top-8 h-[45px] object-contain'
                />
                <div className='grid place-items-center gap-8'>
                    <img src={LoginHero} alt='login hero' />
                    <h1 className='text-[52px] font-bold text-white'>
                        Login your account
                    </h1>
                </div>
            </div>
            <LoginForm />
        </div>
    );
}

export default LoginPage;
