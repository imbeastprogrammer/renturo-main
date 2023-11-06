import LoginOtpForm from './components/LoginOtpForm';
import LoginOtpHero from './components/LoginOtpHero';

function LoginOtp() {
    return (
        <div className='grid h-screen place-items-center p-4'>
            <div className='grid h-full max-h-[600px] w-full max-w-5xl grid-cols-[400px_1fr] gap-4 rounded-xl border p-4 shadow-lg'>
                <LoginOtpHero />
                <LoginOtpForm />
            </div>
        </div>
    );
}

export default LoginOtp;
