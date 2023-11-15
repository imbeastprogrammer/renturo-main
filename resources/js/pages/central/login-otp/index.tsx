import LoginOtpForm from './components/LoginOtpForm';
import LoginOtpHero from './components/LoginOtpHero';

function LoginOtp() {
    return (
        <div className='grid h-screen w-full grid-cols-[610px_1fr] gap-8 font-outfit'>
            <LoginOtpHero />
            <LoginOtpForm />
        </div>
    );
}

export default LoginOtp;
