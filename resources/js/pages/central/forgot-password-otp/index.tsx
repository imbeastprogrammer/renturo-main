import ForgotPasswordOtpForm from './components/ForgotPasswordOtpForm';
import ForgotPasswordOtpHero from './components/ForgotPasswordOtpHero';

function ForgotPasswordOtp() {
    return (
        <div className='grid h-screen w-full grid-cols-[610px_1fr] gap-8 font-outfit'>
            <ForgotPasswordOtpHero />
            <ForgotPasswordOtpForm />
        </div>
    );
}

export default ForgotPasswordOtp;
