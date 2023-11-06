import ForgotPasswordOtpForm from './components/ForgotPasswordOtpForm';
import ForgotPasswordOtpHero from './components/ForgotPasswordOtpHero';

function ForgotPasswordOtp() {
    return (
        <div className='grid h-screen place-items-center p-4'>
            <div className='grid h-full max-h-[700px] w-full max-w-6xl grid-cols-[400px_1fr] rounded-3xl border p-4 shadow-lg'>
                <ForgotPasswordOtpHero />
                <ForgotPasswordOtpForm />
            </div>
        </div>
    );
}

export default ForgotPasswordOtp;
